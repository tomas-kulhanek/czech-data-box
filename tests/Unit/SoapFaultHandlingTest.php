<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\DTO\Request\BigMessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Request\MessageEnvelopeDownload;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;
use TomasKulhanek\CzechDataBox\Exception\ConnectionException;
use TomasKulhanek\CzechDataBox\Exception\CzechDataBoxException;
use TomasKulhanek\CzechDataBox\Exception\SoapFault;
use TomasKulhanek\CzechDataBox\Exception\SystemExclusion;
use TomasKulhanek\CzechDataBox\Provider\ClientProviderInterface;
use TomasKulhanek\CzechDataBox\Provider\EndpointProvider;
use TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider;
use TomasKulhanek\CzechDataBox\Provider\SymfonyClientProvider;
use TomasKulhanek\Tests\CzechDataBox\SerializerTrait;

final class SoapFaultHandlingTest extends TestCase
{
    use SerializerTrait;

    private const string SOAP_11_FAULT = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
  <SOAP-ENV:Body>
    <SOAP-ENV:Fault>
      <faultcode>SOAP-ENV:Client</faultcode>
      <faultstring>Authentication failed</faultstring>
    </SOAP-ENV:Fault>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;

    private const string SOAP_12_FAULT = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope">
  <env:Body>
    <env:Fault>
      <env:Code>
        <env:Value>env:Receiver</env:Value>
      </env:Code>
      <env:Reason>
        <env:Text xml:lang="en">Attachment storage is not available</env:Text>
      </env:Reason>
    </env:Fault>
  </env:Body>
</env:Envelope>
XML;

    private function createAccount(): Account
    {
        $account = new Account();
        $account->setLoginType(LoginTypeEnum::NAME_PASSWORD);
        $account->setLoginName('user');
        $account->setPassword('password');

        return $account;
    }

    private function createGuzzleProvider(Response $response): GuzzleClientProvider
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([$response]))]);

        return new GuzzleClientProvider($client, EndpointProvider::test());
    }

    private function createSymfonyProvider(MockResponse $response): SymfonyClientProvider
    {
        return new SymfonyClientProvider(new MockHttpClient($response), EndpointProvider::test());
    }

    private function assertSoap11FaultIsDetected(ClientProviderInterface $provider): void
    {
        $connector = new Connector(self::createSerializer(), $provider);
        $request = new MessageEnvelopeDownload();
        $request->setDataMessageId('123456789');

        try {
            $connector->messageEnvelopeDownload($this->createAccount(), $request);
            self::fail('Expected a SoapFault to be thrown.');
        } catch (SoapFault $soapFault) {
            self::assertSame('SOAP-ENV:Client', $soapFault->faultCode);
            self::assertSame('Authentication failed', $soapFault->faultString);
            self::assertStringContainsString('SOAP-ENV:Client', $soapFault->getMessage());
            self::assertStringContainsString('Authentication failed', $soapFault->getMessage());
        }
    }

    public function testGuzzleProviderMaps503ToSystemExclusion(): void
    {
        $provider = $this->createGuzzleProvider(new Response(503, [], 'Service Temporarily Unavailable'));

        $this->expectException(SystemExclusion::class);
        $provider->sendRequest($this->createAccount(), ServiceTypeEnum::INFO, '<request/>');
    }

    public function testSymfonyProviderMaps503ToSystemExclusion(): void
    {
        $provider = $this->createSymfonyProvider(new MockResponse('Service Temporarily Unavailable', ['http_code' => 503]));

        $this->expectException(SystemExclusion::class);
        $provider->sendRequest($this->createAccount(), ServiceTypeEnum::INFO, '<request/>');
    }

    public function testGuzzleProviderPassesHttp500FaultBodyToConnector(): void
    {
        $this->assertSoap11FaultIsDetected(
            $this->createGuzzleProvider(new Response(500, ['Content-Type' => 'text/xml'], self::SOAP_11_FAULT))
        );
    }

    public function testSymfonyProviderPassesHttp500FaultBodyToConnector(): void
    {
        $this->assertSoap11FaultIsDetected(
            $this->createSymfonyProvider(new MockResponse(self::SOAP_11_FAULT, ['http_code' => 500]))
        );
    }

    public function testSoap12FaultOnVodzOperationIsDetected(): void
    {
        $connector = new Connector(self::createSerializer(), new RecordingClientProvider(self::SOAP_12_FAULT));
        $request = new BigMessageDownload();
        $request->setDataMessageId('123456789');

        try {
            $connector->bigMessageDownload($this->createAccount(), $request);
            self::fail('Expected a SoapFault to be thrown.');
        } catch (SoapFault $soapFault) {
            self::assertSame('env:Receiver', $soapFault->faultCode);
            self::assertSame('Attachment storage is not available', $soapFault->faultString);
        }
    }

    public function testSoap11FaultWithHttp200IsDetected(): void
    {
        $this->assertSoap11FaultIsDetected(new RecordingClientProvider(self::SOAP_11_FAULT));
    }

    public function testSoapFaultIsConnectionAndCzechDataBoxException(): void
    {
        $soapFault = new SoapFault('SOAP-ENV:Server', 'Internal error');

        self::assertInstanceOf(ConnectionException::class, $soapFault);
        self::assertInstanceOf(CzechDataBoxException::class, $soapFault);
        self::assertSame('SOAP-ENV:Server', $soapFault->faultCode);
        self::assertSame('Internal error', $soapFault->faultString);
    }
}
