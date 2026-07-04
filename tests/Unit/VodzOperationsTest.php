<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\DTO\BigMessageEnvelope;
use TomasKulhanek\CzechDataBox\DTO\BigMessageFiles;
use TomasKulhanek\CzechDataBox\DTO\ExtFile;
use TomasKulhanek\CzechDataBox\DTO\File;
use TomasKulhanek\CzechDataBox\DTO\Request\BigMessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Request\CreateBigMessage;
use TomasKulhanek\CzechDataBox\DTO\Request\MessageEnvelopeDownload;
use TomasKulhanek\CzechDataBox\DTO\Response\UploadAttachment;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;
use TomasKulhanek\CzechDataBox\Provider\ClientProviderInterface;
use TomasKulhanek\Serializer\SerializerFactory;

class VodzOperationsTest extends TestCase
{
    private function createAccount(): Account
    {
        $account = new Account();
        $account->setProduction(false);
        $account->setLoginType(LoginTypeEnum::NAME_PASSWORD);

        return $account;
    }

    private function createFakeProvider(string $soapResponse): ClientProviderInterface
    {
        return new class ($soapResponse) implements ClientProviderInterface {
            public ?string $capturedBody = null;

            public ?ServiceTypeEnum $capturedServiceType = null;

            public function __construct(private readonly string $response)
            {
            }

            public function sendRequest(Account $account, ServiceTypeEnum $serviceType, string $xmlBody): string
            {
                $this->capturedBody = $xmlBody;
                $this->capturedServiceType = $serviceType;

                return $this->response;
            }
        };
    }

    public function testVodzOperationUsesSoap12Envelope(): void
    {
        $soapResponse = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
  <soap:Body>
    <p:BigMessageDownloadResponse xmlns:p="https://isds.czechpoint.cz/v20">
      <p:dmStatus>
        <p:dmStatusCode>0000</p:dmStatusCode>
        <p:dmStatusMessage>Operation successfully</p:dmStatusMessage>
      </p:dmStatus>
    </p:BigMessageDownloadResponse>
  </soap:Body>
</soap:Envelope>
XML;
        $provider = $this->createFakeProvider($soapResponse);
        $connector = new Connector(SerializerFactory::create(), $provider);

        $request = new BigMessageDownload();
        $request->setDataMessageId('123456789');
        $response = $connector->bigMessageDownload($this->createAccount(), $request);

        self::assertNotNull($provider->capturedBody);
        self::assertStringContainsString('http://www.w3.org/2003/05/soap-envelope', $provider->capturedBody);
        self::assertStringNotContainsString('schemas.xmlsoap.org/soap/envelope', $provider->capturedBody);
        self::assertStringContainsString('BigMessageDownload', $provider->capturedBody);
        self::assertSame(ServiceTypeEnum::VODZ, $provider->capturedServiceType);
        self::assertNull($response->getReturnedMessage());
        self::assertSame('0000', $response->getStatus()->getCode());
        self::assertTrue($response->getStatus()->isOk());
    }

    public function testSoap11OperationKeepsSoap11Envelope(): void
    {
        $soapResponse = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
  <SOAP-ENV:Body>
    <p:MessageEnvelopeDownloadResponse xmlns:p="https://isds.czechpoint.cz/v20">
      <p:dmStatus>
        <p:dmStatusCode>0000</p:dmStatusCode>
        <p:dmStatusMessage>Operation successfully</p:dmStatusMessage>
      </p:dmStatus>
    </p:MessageEnvelopeDownloadResponse>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;
        $provider = $this->createFakeProvider($soapResponse);
        $connector = new Connector(SerializerFactory::create(), $provider);

        $request = new MessageEnvelopeDownload();
        $request->setDataMessageId('123456789');
        $response = $connector->messageEnvelopeDownload($this->createAccount(), $request);

        self::assertNotNull($provider->capturedBody);
        self::assertStringContainsString('http://schemas.xmlsoap.org/soap/envelope/', $provider->capturedBody);
        self::assertStringNotContainsString('http://www.w3.org/2003/05/soap-envelope', $provider->capturedBody);
        self::assertSame(ServiceTypeEnum::INFO, $provider->capturedServiceType);
        self::assertTrue($response->getStatus()->isOk());
    }

    public function testCreateBigMessageRequestIsSerialized(): void
    {
        $serializer = SerializerFactory::create();

        $envelope = new BigMessageEnvelope();
        $envelope->setType('V');
        $envelope->setRecipientId('abcdefg');
        $envelope->setAnnotation('Testovací VoDZ');
        $envelope->setPersonalDelivery(false);

        $extFile = new ExtFile();
        $extFile->setMetaType('main');
        $extFile->setAttachmentId('ATT123');
        $extFile->setAttachmentHash1('aaaa');
        $extFile->setAttachmentHash1Algorithm('SHA-256');
        $extFile->setAttachmentHash2('bbbb');
        $extFile->setAttachmentHash2Algorithm('SHA-512');

        $inlineFile = new File();
        $inlineFile->setMimeType('text/plain');
        $inlineFile->setMetaType('enclosure');
        $inlineFile->setDescription('priloha.txt');
        $inlineFile->setXmlContent('obsah');

        $files = new BigMessageFiles();
        $files->addExtFile($extFile);
        $files->addFile($inlineFile);

        $request = new CreateBigMessage();
        $request->setEnvelope($envelope);
        $request->setFiles($files);

        $xml = $serializer->serialize($request, 'xml');

        self::assertStringContainsString('CreateBigMessage', $xml);
        self::assertStringContainsString('dmType="V"', $xml);
        self::assertStringContainsString('<p:dbIDRecipient>abcdefg</p:dbIDRecipient>', $xml);
        self::assertStringContainsString('<p:dmAnnotation>Testovací VoDZ</p:dmAnnotation>', $xml);
        self::assertStringContainsString('<p:dmPersonalDelivery>false</p:dmPersonalDelivery>', $xml);
        self::assertStringContainsString('dmAttID="ATT123"', $xml);
        self::assertStringContainsString('dmAttHash1="aaaa"', $xml);
        self::assertStringContainsString('dmAttHash1Alg="SHA-256"', $xml);
        self::assertStringContainsString('dmAttHash2="bbbb"', $xml);
        self::assertStringContainsString('dmAttHash2Alg="SHA-512"', $xml);
        self::assertStringContainsString('<p:dmXMLContent>obsah</p:dmXMLContent>', $xml);
        self::assertMatchesRegularExpression(
            '~<p:dmFiles[^>]*>\s*<p:dmExtFile [^>]*/>\s*<p:dmFile [^>]*>.*</p:dmFile>\s*</p:dmFiles>~s',
            $xml,
            'dmFiles must contain dmExtFile entries followed by dmFile entries in a single wrapper'
        );
        self::assertLessThan(
            (int) strpos((string) $xml, '<p:dmFiles'),
            (int) strpos((string) $xml, '<p:dmEnvelope'),
            'dmEnvelope must be serialized before dmFiles'
        );
    }

    public function testUploadAttachmentResponseIsDeserialized(): void
    {
        $serializer = SerializerFactory::create();
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<p:UploadAttachmentResponse xmlns:p="https://isds.czechpoint.cz/v20">
  <p:dmAttID>ATT123456</p:dmAttID>
  <p:dmAttHash1 AttHashAlg="SHA-256">1a2b3c4d</p:dmAttHash1>
  <p:dmAttHash2 AttHashAlg="SHA-512">5e6f7a8b</p:dmAttHash2>
  <p:dmStatus>
    <p:dmStatusCode>0000</p:dmStatusCode>
    <p:dmStatusMessage>Operation successfully</p:dmStatusMessage>
  </p:dmStatus>
</p:UploadAttachmentResponse>
XML;
        $response = $serializer->deserialize($xml, UploadAttachment::class, 'xml');
        self::assertSame('ATT123456', $response->getAttachmentId());
        self::assertNotNull($response->getAttachmentHash1());
        self::assertSame('1a2b3c4d', $response->getAttachmentHash1()->getValue());
        self::assertSame('SHA-256', $response->getAttachmentHash1()->getAlgorithm());
        self::assertNotNull($response->getAttachmentHash2());
        self::assertSame('5e6f7a8b', $response->getAttachmentHash2()->getValue());
        self::assertSame('SHA-512', $response->getAttachmentHash2()->getAlgorithm());
        self::assertSame('0000', $response->getStatus()->getCode());
        self::assertTrue($response->getStatus()->isOk());
    }
}
