<?php

declare(strict_types=1);


namespace TomasKulhanek\Tests\CzechDataBox\Integration;

use GuzzleHttp\Client;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpClient\HttpClient;
use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\Provider\EndpointProvider;
use TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider;
use TomasKulhanek\CzechDataBox\Provider\SymfonyClientProvider;
use TomasKulhanek\Serializer\SerializerFactory;

trait ConnectorTrait
{
	protected function createSerializer(): SerializerInterface
	{
		return SerializerFactory::create();
	}

	protected function createGuzzleProvider(): GuzzleClientProvider
	{
		$endpointProvider = new EndpointProvider();
		return new GuzzleClientProvider(new Client(), $endpointProvider);
	}

	protected function createSymfonyProvider(): SymfonyClientProvider
	{
		$endpointProvider = new EndpointProvider();
		return new SymfonyClientProvider(HttpClient::create(), $endpointProvider);
	}

	private function createGuzzleConnector(): Connector
	{
		return new Connector(
			$this->createSerializer(),
			$this->createGuzzleProvider()
		);
	}

	private function createSymfonyConnector(): Connector
	{
		return new Connector(
			$this->createSerializer(),
			$this->createSymfonyProvider()
		);
	}
}
