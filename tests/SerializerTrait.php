<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox;

use JMS\Serializer\SerializerInterface;
use LogicException;
use TomasKulhanek\CzechDataBox\Serializer\SerializerFactory;

trait SerializerTrait
{
    private static function createSerializer(): SerializerInterface
    {
        return SerializerFactory::create();
    }

    /**
     * @template T of object
     * @param class-string<T> $type
     * @return T
     */
    private static function deserializeXml(string $xml, string $type): object
    {
        $result = self::createSerializer()->deserialize($xml, $type, 'xml');
        if (!$result instanceof $type) {
            throw new LogicException(sprintf('Expected %s, got %s.', $type, get_debug_type($result)));
        }

        return $result;
    }
}
