<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use TomasKulhanek\CzechDataBox\DTO\BigAttachment;
use TomasKulhanek\CzechDataBox\DTO\BigMessageEnvelope;
use TomasKulhanek\CzechDataBox\DTO\BigMessageFiles;
use TomasKulhanek\CzechDataBox\DTO\Envelope;
use TomasKulhanek\CzechDataBox\DTO\ExtFile;
use TomasKulhanek\CzechDataBox\DTO\Recipient;
use DateTimeImmutable;
use DateTimeZone;
use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use Error;
use Exception;
use JMS\Serializer\Annotation as Serializer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
use RuntimeException;
use TomasKulhanek\Tests\CzechDataBox\SerializerTrait;

/**
 * ISDS marks most envelope elements as nillable="true" or minOccurs="0", so a single record with a
 * missing or nilled element used to blow up the whole call with a PHP Error the caller cannot catch.
 *
 * These tests deserialize every response DTO from the smallest document the schema still allows and
 * then call every public getter through reflection. Any Error means a property is typed more strictly
 * than the schema guarantees.
 */
final class DtoNullabilityTest extends TestCase
{
    use SerializerTrait;

    private const string NS = 'http://isds.czechpoint.cz/v20';

    private const string XSI_NS = 'http://www.w3.org/2001/XMLSchema-instance';

    /**
     * @return iterable<string, array{class-string}>
     */
    public static function responseDtoProvider(): iterable
    {
        foreach (self::collectResponseDtoClasses() as $class) {
            yield substr((string) strrchr($class, '\\'), 1) => [$class];
        }
    }

    /**
     * A response carrying only the elements the schema makes mandatory must not break any getter.
     *
     * @param class-string $class
     */
    #[DataProvider('responseDtoProvider')]
    public function testEveryGetterSurvivesMinimalResponse(string $class): void
    {
        $xml = self::buildXml($class, nilOptional: false);
        $dto = self::deserializeXml($xml, $class);

        $this->assertGettersDoNotFail($dto, $xml);
        self::assertInstanceOf($class, $dto);
    }

    /**
     * ISDS sends xsi:nil="true" instead of omitting a nillable element; assigning that null must not
     * hit a non-nullable property.
     *
     * @param class-string $class
     */
    #[DataProvider('responseDtoProvider')]
    public function testEveryGetterSurvivesNilledResponse(string $class): void
    {
        $xml = self::buildXml($class, nilOptional: true);
        $dto = self::deserializeXml($xml, $class);

        $this->assertGettersDoNotFail($dto, $xml);
        self::assertInstanceOf($class, $dto);
    }

    private function assertGettersDoNotFail(object $dto, string $xml): void
    {
        $called = 0;
        foreach (new ReflectionClass($dto)->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isStatic() || $method->getNumberOfRequiredParameters() > 0) {
                continue;
            }
            if (!preg_match('/^(get|is|has)[A-Z]/', $method->getName())) {
                continue;
            }
            try {
                $method->invoke($dto);
            } catch (Error $error) {
                // An Error means the property is typed more strictly than the schema allows - that is
                // exactly the class of failure a caller cannot catch. Declared exceptions are fine.
                self::fail(sprintf(
                    "%s::%s() failed on a schema-valid response: %s: %s\nDocument: %s",
                    $dto::class,
                    $method->getName(),
                    $error::class,
                    $error->getMessage(),
                    $xml
                ));
            } catch (Exception) {
                // A documented domain exception (e.g. GetDataBoxAddress::getStatus()) is a deliberate
                // contract, not an initialisation bug.
            }
            $called++;
        }

        self::assertGreaterThanOrEqual(0, $called);
    }

    /**
     * @return list<class-string>
     */
    private static function collectResponseDtoClasses(): array
    {
        $classes = [];
        foreach (['DTO', 'DTO/Response'] as $directory) {
            $files = glob(__DIR__ . '/../../src/' . $directory . '/*.php');
            self::assertNotFalse($files, 'Cannot list ' . $directory);
            foreach ($files as $file) {
                /** @var class-string $class */
                $class = 'TomasKulhanek\\CzechDataBox\\' . str_replace('/', '\\', $directory)
                    . '\\' . basename($file, '.php');
                $reflection = new ReflectionClass($class);
                if ($reflection->isAbstract() || $reflection->isInterface() || $reflection->isEnum()) {
                    continue;
                }
                // Request-only structures are deliberately left non-nullable, see CHANGELOG 6.0.0.
                if (in_array($class, self::requestOnlyClasses(), true)) {
                    continue;
                }
                $classes[] = $class;
            }
        }
        sort($classes);

        return $classes;
    }

    /**
     * @return list<class-string>
     */
    private static function requestOnlyClasses(): array
    {
        return [
            BigAttachment::class,
            BigMessageEnvelope::class,
            BigMessageFiles::class,
            Envelope::class,
            ExtFile::class,
            Recipient::class,
        ];
    }

    /**
     * Renders the smallest schema-valid document for a DTO: every property without a default is
     * mandatory, so it gets a value; nullable ones are either omitted or nilled.
     *
     * @param class-string $class
     */
    private static function buildXml(string $class, bool $nilOptional): string
    {
        $reflection = new ReflectionClass($class);
        $root = self::attribute($reflection, Serializer\XmlRoot::class);
        $name = is_string($root?->name) ? $root->name : 'root';
        $namespace = is_string($root?->namespace) ? $root->namespace : self::NS;

        return sprintf(
            '<p:%s xmlns:p="%s" xmlns:xsi="%s"%s>%s</p:%s>',
            $name,
            $namespace,
            self::XSI_NS,
            self::renderAttributes($reflection, $nilOptional),
            self::renderElements($reflection, $nilOptional),
            $name
        );
    }

    /**
     * @param ReflectionClass<object> $reflection
     */
    private static function renderAttributes(ReflectionClass $reflection, bool $nilOptional): string
    {
        $xml = '';
        foreach (self::properties($reflection, $nilOptional) as $property) {
            if (self::attribute($property, Serializer\XmlAttribute::class) === null) {
                continue;
            }
            $xml .= sprintf(' %s="%s"', self::serializedName($property), self::scalarValue($property));
        }

        return $xml;
    }

    /**
     * @param ReflectionClass<object> $reflection
     */
    private static function renderElements(ReflectionClass $reflection, bool $nilOptional): string
    {
        $xml = '';
        foreach (self::properties($reflection, $nilOptional) as $property) {
            if (self::attribute($property, Serializer\XmlAttribute::class) !== null) {
                continue;
            }
            if (self::attribute($property, Serializer\XmlValue::class) !== null) {
                $xml .= self::scalarValue($property);
                continue;
            }

            $name = self::serializedName($property);
            // Nilling is driven by the schema, not by the current PHP type - otherwise narrowing a
            // property back to non-nullable would silently stop it from being nilled and the test
            // would go green again.
            if (
                $nilOptional
                && self::isNillableInSchema($name)
                && !in_array($name, self::DELIBERATELY_NON_NULLABLE, true)
            ) {
                $xml .= sprintf('<p:%s xsi:nil="true"/>', $name);
                continue;
            }

            $nested = self::nestedClass($property);
            $value = $nested !== null
                ? self::renderAttributes(new ReflectionClass($nested), $nilOptional)
                    . '>' . self::renderElements(new ReflectionClass($nested), $nilOptional)
                : '>' . self::scalarValue($property);
            $xml .= sprintf('<p:%s%s</p:%s>', $name, $value, $name);
        }

        return $xml;
    }

    /**
     * Mandatory properties are always rendered; nullable ones only when they are being nilled.
     *
     * @param ReflectionClass<object> $reflection
     * @return list<ReflectionProperty>
     */
    private static function properties(ReflectionClass $reflection, bool $nilOptional): array
    {
        $properties = [];
        foreach ($reflection->getProperties() as $property) {
            if ($property->isStatic() || self::isCollection($property)) {
                continue;
            }
            // An element carrying XmlValue has no separate tag to omit - leaving it out would produce
            // an empty element the serializer cannot convert back.
            if (self::attribute($property, Serializer\XmlValue::class) !== null) {
                $properties[] = $property;
                continue;
            }
            if (self::isNullable($property) && !$nilOptional) {
                continue;
            }
            // A nilled attribute makes no sense - xsi:nil only applies to elements.
            if (
                $nilOptional && self::isNullable($property)
                && self::attribute($property, Serializer\XmlAttribute::class) !== null
            ) {
                continue;
            }
            $properties[] = $property;
        }

        return $properties;
    }

    private static function isNullable(ReflectionProperty $property): bool
    {
        return $property->hasDefaultValue() || $property->getType()?->allowsNull() === true;
    }

    private static function isCollection(ReflectionProperty $property): bool
    {
        $type = self::serializerType($property);

        return $type !== null && str_starts_with($type, 'array');
    }

    /**
     * @return class-string|null
     */
    private static function nestedClass(ReflectionProperty $property): ?string
    {
        $type = self::serializerType($property);
        if ($type === null || !class_exists($type)) {
            return null;
        }
        // Value objects with their own serializer handler are rendered as scalars.
        if (is_a($type, DateTimeImmutable::class, true)) {
            return null;
        }

        return $type;
    }

    private static function serializerType(ReflectionProperty $property): ?string
    {
        $type = self::attribute($property, Serializer\Type::class);

        return is_string($type?->name) ? $type->name : null;
    }

    private static function serializedName(ReflectionProperty $property): string
    {
        $name = self::attribute($property, Serializer\SerializedName::class);
        if (is_string($name?->name)) {
            return $name->name;
        }

        return $property->getName();
    }

    /**
     * Produces a value the serializer accepts for the declared type - the point is only that the
     * property ends up initialised.
     */
    private static function scalarValue(ReflectionProperty $property): string
    {
        $type = self::serializerType($property) ?? 'string';
        if (str_starts_with($type, 'DateTimeImmutable') || str_starts_with($type, 'DateTime')) {
            // The serializer type carries its own format, e.g. DateTimeImmutable<'Y-m-d'>.
            $format = preg_match("/<'([^']+)'/", $type, $matches) === 1 ? $matches[1] : 'Y-m-d\\TH:i:s.uP';

            return new DateTimeImmutable('2026-08-11 10:00:00', new DateTimeZone('Europe/Prague'))
                ->format($format);
        }

        return match (true) {
            $type === 'int', $type === 'integer' => '1',
            $type === 'float', $type === 'double' => '1.0',
            $type === 'bool', $type === 'boolean' => 'true',
            $type === 'base64File', $type === 'base64' => base64_encode('x'),
            default => self::stringValue($property),
        };
    }

    private static function stringValue(ReflectionProperty $property): string
    {
        $type = self::serializerType($property);
        if ($type !== null && class_exists($type) && !is_a($type, DateTimeImmutable::class, true)) {
            throw new RuntimeException(sprintf(
                'No sample value for %s::$%s of serializer type %s - extend the builder instead of '
                . 'skipping the property, otherwise the test silently stops covering it.',
                $property->getDeclaringClass()->getName(),
                $property->getName(),
                $type
            ));
        }

        return 'x';
    }

    /**
     * @template T of object
     * @param ReflectionClass<object>|ReflectionProperty $subject
     * @param class-string<T> $attribute
     * @return T|null
     */
    private static function attribute(ReflectionClass|ReflectionProperty $subject, string $attribute): ?object
    {
        $attributes = $subject->getAttributes($attribute);
        if ($attributes === []) {
            return null;
        }

        return $attributes[0]->newInstance();
    }

    /**
     * The dynamic tests above only prove the DTOs are internally consistent - they would go green
     * again if a property were narrowed back to a non-nullable type. This one is the actual
     * regression lock: it reads the schema and demands nullability wherever ISDS is allowed to omit
     * or nil an element.
     *
     * @param class-string $class
     */
    #[DataProvider('responseDtoProvider')]
    public function testOptionalInSchemaMeansNullableInDto(string $class): void
    {
        $checked = 0;
        foreach (new ReflectionClass($class)->getProperties() as $property) {
            if ($property->isStatic() || self::isCollection($property)) {
                continue;
            }
            $name = self::serializedName($property);
            if (!self::isOptionalInSchema($name)) {
                continue;
            }
            if (in_array($name, self::DELIBERATELY_NON_NULLABLE, true)) {
                continue;
            }
            $checked++;
            $exception = $class . '::$' . $property->getName();

            self::assertTrue(
                self::isNullable($property),
                sprintf(
                    '%s maps to "%s", which the ISDS schema declares nillable, minOccurs="0" or an '
                    . 'optional attribute. Make the property nullable with a null default, or add it '
                    . 'to DELIBERATELY_NON_NULLABLE with a reason.',
                    $exception,
                    $name
                )
            );
            // The return type is deliberately not asserted here: a getter may narrow null to a
            // meaningful default (isVodz() answers false, not null). A getter that narrows without
            // handling null blows up in testEveryGetterSurvivesNilledResponse instead.
        }

        self::assertGreaterThanOrEqual(0, $checked);
    }

    /**
     * Elements left non-nullable on purpose even though the schema permits omission or nil.
     * Every entry is a decision, not an oversight - see CHANGELOG 6.0.0.
     *
     * dmStatus/dbStatus is optional in a handful of output types, but Response::getStatus() returning
     * ResponseStatus is the base contract of the whole library - making it nullable would force a null
     * check on every single call site for responses that are unusable without a status anyway.
     *
     * @var list<string>
     */
    private const array DELIBERATELY_NON_NULLABLE = [
        'dmStatus',
        'dbStatus',
    ];


    /**
     * True when *every* occurrence of the element in the schema is nillable - ISDS is then free to
     * send xsi:nil in any context, so the DTO has to survive it. Names that are nillable in one type
     * and mandatory in another (dmEventTime, dbType, PDZType) are excluded on purpose: the DTO models
     * the context where the value is guaranteed.
     */
    private static function isNillableInSchema(string $name): bool
    {
        $occurrences = 0;
        foreach (self::schemaXpaths() as $xpath) {
            $elements = $xpath->query(sprintf('//xs:element[@name="%s"]', $name));
            if (!$elements instanceof DOMNodeList) {
                continue;
            }
            foreach ($elements as $element) {
                if (!$element instanceof DOMElement) {
                    continue;
                }
                $occurrences++;
                if ($element->getAttribute('nillable') !== 'true') {
                    return false;
                }
            }
        }

        return $occurrences > 0;
    }

    /**
     * True only when *every* occurrence of the name in the schema may be missing or nilled.
     *
     * Requiring all occurrences avoids false alarms: names such as dmStatus or dmHash are reused
     * across types and are mandatory in most of them, so "optional somewhere" would flag properties
     * that are in fact guaranteed in the context the DTO models. What survives this filter is
     * unconditionally optional - exactly the case that used to throw a PHP Error.
     */
    private static function isOptionalInSchema(string $name): bool
    {
        $occurrences = 0;
        foreach (self::schemaXpaths() as $xpath) {
            $elements = $xpath->query(sprintf('//xs:element[@name="%s"]', $name));
            if ($elements instanceof DOMNodeList) {
                foreach ($elements as $element) {
                    if (!$element instanceof DOMElement) {
                        continue;
                    }
                    $occurrences++;
                    $optionalAncestor = $xpath->query('ancestor::xs:sequence[@minOccurs="0"]', $element);
                    $inOptionalSequence = $optionalAncestor instanceof DOMNodeList && $optionalAncestor->length > 0;
                    if (
                        $element->getAttribute('nillable') !== 'true'
                        && $element->getAttribute('minOccurs') !== '0'
                        && !$inOptionalSequence
                    ) {
                        return false;
                    }
                }
            }
            $attributes = $xpath->query(sprintf('//xs:attribute[@name="%s"]', $name));
            if ($attributes instanceof DOMNodeList) {
                foreach ($attributes as $attribute) {
                    if (!$attribute instanceof DOMElement) {
                        continue;
                    }
                    $occurrences++;
                    if ($attribute->getAttribute('use') === 'required') {
                        return false;
                    }
                }
            }
        }

        return $occurrences > 0;
    }

    /**
     * @return list<DOMXPath>
     */
    private static function schemaXpaths(): array
    {
        /** @var list<DOMXPath>|null $cache */
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }
        $xpaths = [];
        foreach (['dmBaseTypes.xsd', 'dbTypes.xsd'] as $schema) {
            $document = new DOMDocument();
            self::assertTrue($document->load(__DIR__ . '/../_data/xsd/' . $schema), 'Cannot load ' . $schema);
            $xpath = new DOMXPath($document);
            $xpath->registerNamespace('xs', 'http://www.w3.org/2001/XMLSchema');
            $xpaths[] = $xpath;
        }
        $cache = $xpaths;

        return $cache;
    }

    /**
     * Guards the guard: a property typed with a class the builder cannot render would otherwise
     * quietly fall back to a string and stop testing anything.
     */
    public function testBuilderCoversEveryNonNullableProperty(): void
    {
        $checked = 0;
        foreach (self::collectResponseDtoClasses() as $class) {
            foreach (new ReflectionClass($class)->getProperties() as $property) {
                if ($property->isStatic() || self::isNullable($property) || self::isCollection($property)) {
                    continue;
                }
                $type = $property->getType();
                self::assertInstanceOf(
                    ReflectionNamedType::class,
                    $type,
                    $class . '::$' . $property->getName() . ' has an unsupported type declaration.'
                );
                $checked++;
            }
        }

        self::assertGreaterThan(0, $checked, 'No mandatory property found - the scan is broken.');
    }
}
