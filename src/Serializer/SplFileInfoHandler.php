<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use SimpleXMLElement;
use Stringable;

use function base64_decode;
use function base64_encode;
use function is_scalar;
use function trim;

final class SplFileInfoHandler implements SubscribingHandlerInterface
{
    public const string TYPE = 'base64File';

    private const array FORMATS = ['xml', 'json'];

    /**
     * @return list<array{direction: int, type: string, format: string, method: string}>
     */
    public static function getSubscribingMethods(): array
    {
        $methods = [];
        foreach (self::FORMATS as $format) {
            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'type' => self::TYPE,
                'format' => $format,
                'method' => 'serialize',
            ];
            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'type' => self::TYPE,
                'format' => $format,
                'method' => 'deserialize',
            ];
        }

        return $methods;
    }

    /**
     * @param array{name: string, params: array<mixed>} $type
     */
    public function serialize(
        SerializationVisitorInterface $visitor,
        SplFileInfo $file,
        array $type,
        Context $context
    ): mixed {
        return $visitor->visitString(base64_encode($file->getContents()), $type);
    }

    /**
     * @param array{name: string, params: array<mixed>} $type
     */
    public function deserialize(
        DeserializationVisitorInterface $visitor,
        mixed $data,
        array $type,
        Context $context
    ): ?SplFileInfo {
        $encoded = self::toString($data);
        if ($encoded === '') {
            return null;
        }
        $decoded = base64_decode($encoded, true);
        if ($decoded === false) {
            return null;
        }

        return SplFileInfo::createInTemp($decoded);
    }

    /**
     * The XML visitor hands over a SimpleXMLElement, the JSON one a plain string.
     */
    private static function toString(mixed $data): string
    {
        if (is_scalar($data)) {
            return trim((string) $data);
        }
        if ($data instanceof SimpleXMLElement || $data instanceof Stringable) {
            return trim((string) $data);
        }

        return '';
    }
}
