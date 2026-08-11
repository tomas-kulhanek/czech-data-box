<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Serializer;

use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;

final class SerializerFactory
{
    public static function create(): SerializerInterface
    {
        return SerializerBuilder::create()
            ->setPropertyNamingStrategy(
                new SerializedNameAnnotationStrategy(new IdenticalPropertyNamingStrategy())
            )
            ->addDefaultHandlers()
            ->configureHandlers(static function (HandlerRegistry $registry): void {
                $registry->registerSubscribingHandler(new SplFileInfoHandler());
            })
            ->build();
    }
}
