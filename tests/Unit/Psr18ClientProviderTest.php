<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;
use TomasKulhanek\CzechDataBox\Exception\ConnectionException;
use TomasKulhanek\CzechDataBox\Exception\SystemExclusion;
use TomasKulhanek\CzechDataBox\Provider\EndpointProvider;
use TomasKulhanek\CzechDataBox\Provider\Psr18ClientProvider;

class Psr18ClientProviderTest extends TestCase
{
    /**
     * @return ClientInterface&object{lastRequest: RequestInterface|null}
     */
    private function createRecordingClient(ResponseInterface $response): ClientInterface
    {
        return new class ($response) implements ClientInterface {
            public ?RequestInterface $lastRequest = null;

            public function __construct(private readonly ResponseInterface $response)
            {
            }

            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                $this->lastRequest = $request;

                return $this->response;
            }
        };
    }

    private function createProvider(ClientInterface $client, ?EndpointProvider $endpointProvider = null): Psr18ClientProvider
    {
        $factory = new Psr17Factory();

        return new Psr18ClientProvider($client, $factory, $factory, $endpointProvider);
    }

    private function createNamePasswordAccount(): Account
    {
        $account = new Account();
        $account->setLoginType(LoginTypeEnum::NAME_PASSWORD);
        $account->setLoginName('user');
        $account->setPassword('pass');

        return $account;
    }

    public function testRequestUrlMatchesEndpointProvider(): void
    {
        $client = $this->createRecordingClient(new Response(200, [], '<ok/>'));
        $provider = $this->createProvider($client, EndpointProvider::test());
        $account = $this->createNamePasswordAccount();

        $provider->sendRequest($account, ServiceTypeEnum::INFO, '<x/>');
        self::assertNotNull($client->lastRequest);
        self::assertSame('https://ws1.datovka-test.gov.cz/DS/dx', (string) $client->lastRequest->getUri());
        self::assertSame('POST', $client->lastRequest->getMethod());

        $provider->sendRequest($account, ServiceTypeEnum::VODZ, '<x/>');
        self::assertNotNull($client->lastRequest);
        self::assertSame('https://ws2.datovka-test.gov.cz/DS/vodz', (string) $client->lastRequest->getUri());
    }

    public function testDefaultEndpointProviderIsProduction(): void
    {
        $client = $this->createRecordingClient(new Response(200, [], '<ok/>'));
        $provider = $this->createProvider($client);

        $provider->sendRequest($this->createNamePasswordAccount(), ServiceTypeEnum::INFO, '<x/>');
        self::assertNotNull($client->lastRequest);
        self::assertSame('https://ws1.datovka.gov.cz/DS/dx', (string) $client->lastRequest->getUri());
    }

    public function testSoap11Headers(): void
    {
        $client = $this->createRecordingClient(new Response(200, [], '<ok/>'));
        $provider = $this->createProvider($client, EndpointProvider::test());

        $provider->sendRequest($this->createNamePasswordAccount(), ServiceTypeEnum::OPERATIONS, '<x/>');
        self::assertNotNull($client->lastRequest);
        self::assertSame('text/xml; charset=utf-8', $client->lastRequest->getHeaderLine('Content-Type'));
        self::assertSame('""', $client->lastRequest->getHeaderLine('SOAPAction'));
    }

    public function testSoap12Headers(): void
    {
        $client = $this->createRecordingClient(new Response(200, [], '<ok/>'));
        $provider = $this->createProvider($client, EndpointProvider::test());

        $provider->sendRequest($this->createNamePasswordAccount(), ServiceTypeEnum::VODZ, '<x/>');
        self::assertNotNull($client->lastRequest);
        self::assertSame('application/soap+xml; charset=utf-8', $client->lastRequest->getHeaderLine('Content-Type'));
        self::assertFalse($client->lastRequest->hasHeader('SOAPAction'));
    }

    public function testAuthorizationHeaderForNamePasswordLogin(): void
    {
        $client = $this->createRecordingClient(new Response(200, [], '<ok/>'));
        $provider = $this->createProvider($client, EndpointProvider::test());

        $provider->sendRequest($this->createNamePasswordAccount(), ServiceTypeEnum::INFO, '<x/>');
        self::assertNotNull($client->lastRequest);
        self::assertSame(
            sprintf('Basic %s', base64_encode('user:pass')),
            $client->lastRequest->getHeaderLine('Authorization')
        );
    }

    public function testBodyPassthrough(): void
    {
        $client = $this->createRecordingClient(new Response(200, [], '<response/>'));
        $provider = $this->createProvider($client, EndpointProvider::test());

        $result = $provider->sendRequest($this->createNamePasswordAccount(), ServiceTypeEnum::INFO, '<request/>');

        self::assertNotNull($client->lastRequest);
        self::assertSame('<request/>', (string) $client->lastRequest->getBody());
        self::assertSame('<response/>', $result);
    }

    public function testServiceUnavailableThrowsSystemExclusion(): void
    {
        $client = $this->createRecordingClient(new Response(503, [], 'maintenance'));
        $provider = $this->createProvider($client, EndpointProvider::test());

        $this->expectException(SystemExclusion::class);

        $provider->sendRequest($this->createNamePasswordAccount(), ServiceTypeEnum::INFO, '<x/>');
    }

    public function testErrorStatusWithBodyReturnsBody(): void
    {
        $client = $this->createRecordingClient(new Response(500, [], '<soap:Fault/>'));
        $provider = $this->createProvider($client, EndpointProvider::test());

        $result = $provider->sendRequest($this->createNamePasswordAccount(), ServiceTypeEnum::INFO, '<x/>');

        self::assertSame('<soap:Fault/>', $result);
    }

    public function testErrorStatusWithEmptyBodyThrowsConnectionException(): void
    {
        $client = $this->createRecordingClient(new Response(500, [], ''));
        $provider = $this->createProvider($client, EndpointProvider::test());

        $this->expectException(ConnectionException::class);

        $provider->sendRequest($this->createNamePasswordAccount(), ServiceTypeEnum::INFO, '<x/>');
    }

    public function testClientExceptionIsWrappedInConnectionException(): void
    {
        $client = new class implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                throw new class ('network failure') extends RuntimeException implements ClientExceptionInterface {
                };
            }
        };
        $provider = $this->createProvider($client, EndpointProvider::test());

        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage('network failure');

        $provider->sendRequest($this->createNamePasswordAccount(), ServiceTypeEnum::INFO, '<x/>');
    }

    public function testCertificateInAccountThrowsConnectionException(): void
    {
        $client = $this->createRecordingClient(new Response(200, [], '<ok/>'));
        $provider = $this->createProvider($client, EndpointProvider::test());

        $account = new Account();
        $account->setLoginType(LoginTypeEnum::SPIS_CERT);
        $account->setPublicKey('-----BEGIN CERTIFICATE-----');
        $account->setPrivateKey('-----BEGIN PRIVATE KEY-----');

        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage('Configure mTLS');

        $provider->sendRequest($account, ServiceTypeEnum::INFO, '<x/>');
    }
}
