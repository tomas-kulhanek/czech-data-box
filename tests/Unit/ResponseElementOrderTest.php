<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Serializer;
use Metadata\MetadataFactoryInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use TomasKulhanek\CzechDataBox\DTO\Response\AddDataBoxUser2;
use TomasKulhanek\CzechDataBox\DTO\Response\ArchiveISDSDocument;
use TomasKulhanek\CzechDataBox\DTO\Response\AuthenticateBigMessage;
use TomasKulhanek\CzechDataBox\DTO\Response\AuthenticateMessage;
use TomasKulhanek\CzechDataBox\DTO\Response\BigMessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Response\ChangeISDSPassword;
use TomasKulhanek\CzechDataBox\DTO\Response\CheckDataBox;
use TomasKulhanek\CzechDataBox\DTO\Response\ClearOpenAddressing;
use TomasKulhanek\CzechDataBox\DTO\Response\CreateBigMessage;
use TomasKulhanek\CzechDataBox\DTO\Response\CreateMessage;
use TomasKulhanek\CzechDataBox\DTO\Response\DataBoxCreditInfo;
use TomasKulhanek\CzechDataBox\DTO\Response\DeleteDataBoxUser2;
use TomasKulhanek\CzechDataBox\DTO\Response\DownloadAttachment;
use TomasKulhanek\CzechDataBox\DTO\Response\DTInfo;
use TomasKulhanek\CzechDataBox\DTO\Response\DummyOperation;
use TomasKulhanek\CzechDataBox\DTO\Response\EraseMessage;
use TomasKulhanek\CzechDataBox\DTO\Response\FindDataBox2;
use TomasKulhanek\CzechDataBox\DTO\Response\GetConstants;
use TomasKulhanek\CzechDataBox\DTO\Response\GetDataBoxActivityStatus;
use TomasKulhanek\CzechDataBox\DTO\Response\GetDataBoxAddress;
use TomasKulhanek\CzechDataBox\DTO\Response\GetDataBoxList;
use TomasKulhanek\CzechDataBox\DTO\Response\GetDataBoxUsers2;
use TomasKulhanek\CzechDataBox\DTO\Response\GetDeliveryInfo;
use TomasKulhanek\CzechDataBox\DTO\Response\GetListForNotifications;
use TomasKulhanek\CzechDataBox\DTO\Response\GetListOfErasedMessages;
use TomasKulhanek\CzechDataBox\DTO\Response\GetListOfReceivedMessages;
use TomasKulhanek\CzechDataBox\DTO\Response\GetListOfSentMessages;
use TomasKulhanek\CzechDataBox\DTO\Response\GetMessageAuthor2;
use TomasKulhanek\CzechDataBox\DTO\Response\GetMessageStateChanges;
use TomasKulhanek\CzechDataBox\DTO\Response\GetOwnerInfoFromLogin;
use TomasKulhanek\CzechDataBox\DTO\Response\GetOwnerInfoFromLogin2;
use TomasKulhanek\CzechDataBox\DTO\Response\GetPasswordInfo;
use TomasKulhanek\CzechDataBox\DTO\Response\GetSignedDeliveryInfo;
use TomasKulhanek\CzechDataBox\DTO\Response\GetUserInfoFromLogin;
use TomasKulhanek\CzechDataBox\DTO\Response\GetUserInfoFromLogin2;
use TomasKulhanek\CzechDataBox\DTO\Response\ISDSSearch3;
use TomasKulhanek\CzechDataBox\DTO\Response\MarkMessageAsDownloaded;
use TomasKulhanek\CzechDataBox\DTO\Response\MessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Response\MessageEnvelopeDownload;
use TomasKulhanek\CzechDataBox\DTO\Response\NewAccessData2;
use TomasKulhanek\CzechDataBox\DTO\Response\PDZInfo;
use TomasKulhanek\CzechDataBox\DTO\Response\PDZSendInfo;
use TomasKulhanek\CzechDataBox\DTO\Response\PickUpAsyncResponse;
use TomasKulhanek\CzechDataBox\DTO\Response\RegisterForNotifications;
use TomasKulhanek\CzechDataBox\DTO\Response\ResignISDSDocument;
use TomasKulhanek\CzechDataBox\DTO\Response\SentMessageEnvelopeDownload;
use TomasKulhanek\CzechDataBox\DTO\Response\SetOpenAddressing;
use TomasKulhanek\CzechDataBox\DTO\Response\SignedBigMessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Response\SignedMessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Response\SignedSentBigMessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Response\SignedSentMessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Response\SuspMessageReport;
use TomasKulhanek\CzechDataBox\DTO\Response\UpdateDataBoxUser2;
use TomasKulhanek\CzechDataBox\DTO\Response\UploadAttachment;
use TomasKulhanek\CzechDataBox\DTO\Response\VerifyMessage;
use TomasKulhanek\CzechDataBox\Serializer\SerializerFactory;

final class ResponseElementOrderTest extends TestCase
{
    private const string DM_BASE_TYPES = 'dmBaseTypes.xsd';

    private const string DB_TYPES = 'dbTypes.xsd';

    /**
     * Response class to the global xs:element of the Provozní řád schema that describes its payload.
     *
     * @var array<class-string, array{string, string}>
     */
    private const array RESPONSE_SCHEMA_ELEMENTS = [
        AddDataBoxUser2::class => [self::DB_TYPES, 'AddDataBoxUser2Response'],
        ArchiveISDSDocument::class => [self::DM_BASE_TYPES, 'ArchiveISDSDocumentResponse'],
        AuthenticateBigMessage::class => [self::DM_BASE_TYPES, 'AuthenticateBigMessageResponse'],
        AuthenticateMessage::class => [self::DM_BASE_TYPES, 'AuthenticateMessageResponse'],
        BigMessageDownload::class => [self::DM_BASE_TYPES, 'BigMessageDownloadResponse'],
        ChangeISDSPassword::class => [self::DB_TYPES, 'ChangeISDSPasswordResponse'],
        CheckDataBox::class => [self::DB_TYPES, 'CheckDataBoxResponse'],
        ClearOpenAddressing::class => [self::DB_TYPES, 'ClearOpenAddressingResponse'],
        CreateBigMessage::class => [self::DM_BASE_TYPES, 'CreateBigMessageResponse'],
        CreateMessage::class => [self::DM_BASE_TYPES, 'CreateMultipleMessageResponse'],
        DataBoxCreditInfo::class => [self::DB_TYPES, 'DataBoxCreditInfoResponse'],
        DeleteDataBoxUser2::class => [self::DB_TYPES, 'DeleteDataBoxUser2Response'],
        DownloadAttachment::class => [self::DM_BASE_TYPES, 'DownloadAttachmentResponse'],
        DTInfo::class => [self::DB_TYPES, 'DTInfoResponse'],
        DummyOperation::class => [self::DM_BASE_TYPES, 'DummyOperationResponse'],
        EraseMessage::class => [self::DM_BASE_TYPES, 'EraseMessageResponse'],
        FindDataBox2::class => [self::DB_TYPES, 'FindDataBox2Response'],
        GetConstants::class => [self::DB_TYPES, 'GetConstantsResponse'],
        GetDataBoxActivityStatus::class => [self::DB_TYPES, 'GetDataBoxActivityStatusResponse'],
        GetDataBoxAddress::class => [self::DB_TYPES, 'GetDataBoxAddressResponse'],
        GetDataBoxList::class => [self::DB_TYPES, 'GetDataBoxListResponse'],
        GetDataBoxUsers2::class => [self::DB_TYPES, 'GetDataBoxUsers2Response'],
        GetDeliveryInfo::class => [self::DM_BASE_TYPES, 'GetDeliveryInfoResponse'],
        GetListForNotifications::class => [self::DM_BASE_TYPES, 'GetListForNotificationsResponse'],
        GetListOfErasedMessages::class => [self::DM_BASE_TYPES, 'GetListOfErasedMessagesResponse'],
        GetListOfReceivedMessages::class => [self::DM_BASE_TYPES, 'GetListOfReceivedMessagesResponse'],
        GetListOfSentMessages::class => [self::DM_BASE_TYPES, 'GetListOfSentMessagesResponse'],
        GetMessageAuthor2::class => [self::DM_BASE_TYPES, 'GetMessageAuthor2Response'],
        GetMessageStateChanges::class => [self::DM_BASE_TYPES, 'GetMessageStateChangesResponse'],
        GetOwnerInfoFromLogin::class => [self::DB_TYPES, 'GetOwnerInfoFromLoginResponse'],
        GetOwnerInfoFromLogin2::class => [self::DB_TYPES, 'GetOwnerInfoFromLogin2Response'],
        GetPasswordInfo::class => [self::DB_TYPES, 'GetPasswordInfoResponse'],
        GetSignedDeliveryInfo::class => [self::DM_BASE_TYPES, 'GetSignedDeliveryInfoResponse'],
        GetUserInfoFromLogin::class => [self::DB_TYPES, 'GetUserInfoFromLoginResponse'],
        GetUserInfoFromLogin2::class => [self::DB_TYPES, 'GetUserInfoFromLogin2Response'],
        ISDSSearch3::class => [self::DB_TYPES, 'ISDSSearch3Response'],
        MarkMessageAsDownloaded::class => [self::DM_BASE_TYPES, 'MarkMessageAsDownloadedResponse'],
        MessageDownload::class => [self::DM_BASE_TYPES, 'MessageDownloadResponse'],
        MessageEnvelopeDownload::class => [self::DM_BASE_TYPES, 'MessageEnvelopeDownloadResponse'],
        NewAccessData2::class => [self::DB_TYPES, 'NewAccessData2Response'],
        PDZInfo::class => [self::DB_TYPES, 'PDZInfoResponse'],
        PDZSendInfo::class => [self::DB_TYPES, 'PDZSendInfoResponse'],
        PickUpAsyncResponse::class => [self::DM_BASE_TYPES, 'PickUpAsyncResponseResponse'],
        RegisterForNotifications::class => [self::DM_BASE_TYPES, 'RegisterForNotificationsResponse'],
        ResignISDSDocument::class => [self::DM_BASE_TYPES, 'Re-signISDSDocumentResponse'],
        SentMessageEnvelopeDownload::class => [self::DM_BASE_TYPES, 'SentMessageEnvelopeDownloadResponse'],
        SetOpenAddressing::class => [self::DB_TYPES, 'SetOpenAddressingResponse'],
        SignedBigMessageDownload::class => [self::DM_BASE_TYPES, 'SignedBigMessageDownloadResponse'],
        SignedMessageDownload::class => [self::DM_BASE_TYPES, 'SignedMessageDownloadResponse'],
        SignedSentBigMessageDownload::class => [self::DM_BASE_TYPES, 'SignedSentBigMessageDownloadResponse'],
        SignedSentMessageDownload::class => [self::DM_BASE_TYPES, 'SignedSentMessageDownloadResponse'],
        SuspMessageReport::class => [self::DM_BASE_TYPES, 'SuspMessageReportResponse'],
        UpdateDataBoxUser2::class => [self::DB_TYPES, 'UpdateDataBoxUser2Response'],
        UploadAttachment::class => [self::DM_BASE_TYPES, 'UploadAttachmentResponse'],
        VerifyMessage::class => [self::DM_BASE_TYPES, 'VerifyMessageResponse'],
    ];

    /**
     * Response classes deliberately left out of the mapping, keyed by the reason.
     *
     * @var array<class-string, string>
     */
    private const array SKIPPED_RESPONSES = [];

    /**
     * Elements a DTO maps although the schema does not declare them; their position is not constrained.
     *
     * @var array<class-string, list<string>>
     */
    private const array TOLERATED_EXTRA_ELEMENTS = [
        GetDataBoxAddress::class => ['dbStatus'],
    ];

    /**
     * @param class-string $class
     */
    #[DataProvider('provideResponses')]
    public function testSerializedOrderMatchesXsdSequence(string $class, string $schemaFile, string $elementName): void
    {
        $schemaOrder = self::readSchemaSequence($schemaFile, $elementName);
        self::assertNotSame([], $schemaOrder, sprintf('Element %s declares no child elements.', $elementName));

        $tolerated = self::TOLERATED_EXTRA_ELEMENTS[$class] ?? [];
        $serializedOrder = self::readSerializedOrder($class);

        $undeclared = array_values(array_diff($serializedOrder, $schemaOrder, $tolerated));
        self::assertSame([], $undeclared, sprintf(
            "%s maps elements the schema type behind %s does not declare: %s\nSchema sequence: %s",
            $class,
            $elementName,
            implode(', ', $undeclared),
            implode(', ', $schemaOrder)
        ));

        $constrained = array_values(array_filter(
            $serializedOrder,
            static fn (string $name): bool => !in_array($name, $tolerated, true)
        ));
        $expected = array_values(array_intersect($schemaOrder, $constrained));

        self::assertSame($expected, $constrained, sprintf(
            "%s serializes its elements in a different order than %s in %s.\n"
            . "Fix the #[Serializer\\AccessorOrder] attribute on the class (it lists PHP property names).\n"
            . "Schema sequence: %s",
            $class,
            $elementName,
            $schemaFile,
            implode(', ', $schemaOrder)
        ));
    }

    public function testEveryResponseClassIsMapped(): void
    {
        $files = glob(__DIR__ . '/../../src/DTO/Response/*.php');
        self::assertNotFalse($files);
        self::assertNotEmpty($files);

        $concreteClasses = [];
        foreach ($files as $file) {
            /** @var class-string $class */
            $class = 'TomasKulhanek\\CzechDataBox\\DTO\\Response\\' . basename($file, '.php');
            if (!class_exists($class) || new ReflectionClass($class)->isAbstract()) {
                continue;
            }
            $concreteClasses[] = $class;
        }

        $missing = array_values(array_diff(
            $concreteClasses,
            array_keys(self::RESPONSE_SCHEMA_ELEMENTS),
            array_keys(self::SKIPPED_RESPONSES)
        ));

        self::assertSame([], $missing, sprintf(
            'Response classes neither mapped in RESPONSE_SCHEMA_ELEMENTS nor skipped: %s',
            implode(', ', $missing)
        ));
    }

    /**
     * @return iterable<string, array{class-string, string, string}>
     */
    public static function provideResponses(): iterable
    {
        foreach (self::RESPONSE_SCHEMA_ELEMENTS as $class => [$schemaFile, $elementName]) {
            yield substr((string) strrchr($class, '\\'), 1) => [$class, $schemaFile, $elementName];
        }
    }

    /**
     * @param class-string $class
     * @return list<string>
     */
    private static function readSerializedOrder(string $class): array
    {
        $serializer = SerializerFactory::create();
        self::assertInstanceOf(Serializer::class, $serializer);

        $factory = new ReflectionProperty(Serializer::class, 'factory')->getValue($serializer);
        self::assertInstanceOf(MetadataFactoryInterface::class, $factory);

        $metadata = $factory->getMetadataForClass($class);
        self::assertInstanceOf(ClassMetadata::class, $metadata);

        $names = [];
        foreach ($metadata->propertyMetadata as $property) {
            self::assertInstanceOf(PropertyMetadata::class, $property);
            self::assertIsString($property->serializedName);
            $names[] = $property->serializedName;
        }

        return $names;
    }

    /**
     * @return list<string>
     */
    private static function readSchemaSequence(string $schemaFile, string $elementName): array
    {
        $document = new DOMDocument();
        self::assertTrue($document->load(__DIR__ . '/../_data/xsd/' . $schemaFile));

        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('xs', 'http://www.w3.org/2001/XMLSchema');

        $elements = $xpath->query(sprintf('/xs:schema/xs:element[@name="%s"]', $elementName));
        self::assertNotFalse($elements);
        self::assertSame(1, $elements->length, sprintf('%s does not declare xs:element %s.', $schemaFile, $elementName));

        $element = $elements->item(0);
        self::assertInstanceOf(DOMElement::class, $element);

        $type = $element->getAttribute('type');
        if ($type === '') {
            $inline = $xpath->query('./xs:complexType', $element);
            self::assertNotFalse($inline);
            self::assertSame(1, $inline->length, sprintf('%s has neither a type nor an inline type.', $elementName));
            $container = $inline->item(0);
        } else {
            $named = $xpath->query(sprintf('/xs:schema/xs:complexType[@name="%s"]', self::localName($type)));
            self::assertNotFalse($named);
            self::assertSame(1, $named->length, sprintf('%s does not define complexType %s.', $schemaFile, $type));
            $container = $named->item(0);
        }

        self::assertInstanceOf(DOMElement::class, $container);

        return self::collectParticles($xpath, $container);
    }

    /**
     * @return list<string>
     */
    private static function collectParticles(DOMXPath $xpath, DOMNode $container): array
    {
        $names = [];
        foreach ($container->childNodes as $child) {
            if (!$child instanceof DOMElement) {
                continue;
            }

            if ($child->localName === 'element') {
                $name = $child->getAttribute('name');
                if ($name !== '') {
                    $names[] = $name;
                }
                continue;
            }

            if ($child->localName === 'group' && $child->hasAttribute('ref')) {
                $reference = self::localName($child->getAttribute('ref'));
                $groups = $xpath->query(sprintf('/xs:schema/xs:group[@name="%s"]', $reference));
                self::assertNotFalse($groups);
                self::assertSame(1, $groups->length, sprintf('Undefined xs:group %s.', $reference));
                $group = $groups->item(0);
                self::assertInstanceOf(DOMElement::class, $group);
                $names = [...$names, ...self::collectParticles($xpath, $group)];
                continue;
            }

            if (in_array($child->localName, ['sequence', 'choice', 'all', 'complexContent', 'extension'], true)) {
                $names = [...$names, ...self::collectParticles($xpath, $child)];
            }
        }

        return $names;
    }

    private static function localName(string $qualifiedName): string
    {
        $position = strrpos($qualifiedName, ':');

        return $position === false ? $qualifiedName : substr($qualifiedName, $position + 1);
    }
}
