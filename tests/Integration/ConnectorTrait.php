<?php

declare(strict_types=1);


namespace TomasKulhanek\Tests\CzechDataBox\Integration;

use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpClient\Psr18Client;
use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\Dispatcher;
use TomasKulhanek\Serializer\SerializerFactory;

trait ConnectorTrait
{
    protected function createSerializer(): SerializerInterface
    {
        return SerializerFactory::create();
    }

    protected function createDispatcher(): Dispatcher
    {
        $psr18Client = new Psr18Client();
        return new Dispatcher($psr18Client, $psr18Client, $psr18Client, $psr18Client);
    }


    private function createConnector(): Connector
    {
        return new Connector(
            $this->createSerializer(),
            $this->createDispatcher()
        );
    }
}
