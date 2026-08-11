<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Integration;

use GuzzleHttp\Client;
use Symfony\Component\HttpClient\HttpClient;
use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\Provider\EndpointProvider;
use TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider;
use TomasKulhanek\CzechDataBox\Provider\SymfonyClientProvider;
use TomasKulhanek\Tests\CzechDataBox\SerializerTrait;

trait ConnectorTrait
{
    use SerializerTrait;

    protected function createGuzzleProvider(): GuzzleClientProvider
    {
        $endpointProvider = EndpointProvider::test();
        return new GuzzleClientProvider(new Client(), $endpointProvider);
    }

    protected function createSymfonyProvider(): SymfonyClientProvider
    {
        $endpointProvider = EndpointProvider::test();
        return new SymfonyClientProvider(HttpClient::create(), $endpointProvider);
    }

    private function createGuzzleConnector(): Connector
    {
        return new Connector(
            self::createSerializer(),
            $this->createGuzzleProvider()
        );
    }

    private function createSymfonyConnector(): Connector
    {
        return new Connector(
            self::createSerializer(),
            $this->createSymfonyProvider()
        );
    }
}
