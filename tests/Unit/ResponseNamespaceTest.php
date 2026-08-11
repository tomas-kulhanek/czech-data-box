<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use TomasKulhanek\Tests\CzechDataBox\SerializerTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\DTO\Request\MessageEnvelopeDownload;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;

class ResponseNamespaceTest extends TestCase
{
    use SerializerTrait;

    private function createAccount(): Account
    {
        $account = new Account();
        $account->setLoginType(LoginTypeEnum::NAME_PASSWORD);

        return $account;
    }

    private function downloadEnvelope(string $soapResponse): RecordingClientProvider
    {
        $provider = new RecordingClientProvider($soapResponse);
        $connector = new Connector(self::createSerializer(), $provider);

        $request = new MessageEnvelopeDownload();
        $request->setDataMessageId('123456789');
        $response = $connector->messageEnvelopeDownload($this->createAccount(), $request);

        self::assertSame('0000', $response->getStatus()->getCode());

        return $provider;
    }

    public function testRequestIsSerializedIntoHttpNamespace(): void
    {
        $provider = $this->downloadEnvelope(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
  <SOAP-ENV:Body>
    <p:MessageEnvelopeDownloadResponse xmlns:p="http://isds.czechpoint.cz/v20">
      <p:dmStatus>
        <p:dmStatusCode>0000</p:dmStatusCode>
        <p:dmStatusMessage>Operation successfully</p:dmStatusMessage>
      </p:dmStatus>
    </p:MessageEnvelopeDownloadResponse>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML);

        self::assertNotNull($provider->capturedBody);
        self::assertStringContainsString('http://isds.czechpoint.cz/v20', $provider->capturedBody);
        self::assertStringNotContainsString('https://isds.czechpoint.cz/v20', $provider->capturedBody);
    }

    public function testResponseWithCustomPrefixIsDeserialized(): void
    {
        $this->downloadEnvelope(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
  <SOAP-ENV:Body>
    <q:MessageEnvelopeDownloadResponse xmlns:q="http://isds.czechpoint.cz/v20">
      <q:dmStatus>
        <q:dmStatusCode>0000</q:dmStatusCode>
        <q:dmStatusMessage>Operation successfully</q:dmStatusMessage>
      </q:dmStatus>
    </q:MessageEnvelopeDownloadResponse>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML);
    }

    public function testResponseWithDefaultNamespaceIsDeserialized(): void
    {
        $this->downloadEnvelope(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
  <SOAP-ENV:Body>
    <MessageEnvelopeDownloadResponse xmlns="http://isds.czechpoint.cz/v20">
      <dmStatus>
        <dmStatusCode>0000</dmStatusCode>
        <dmStatusMessage>Operation successfully</dmStatusMessage>
      </dmStatus>
    </MessageEnvelopeDownloadResponse>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function unprefixedEnvelopeProvider(): array
    {
        return [
            'SOAP 1.1' => ['http://schemas.xmlsoap.org/soap/envelope/'],
            'SOAP 1.2' => ['http://www.w3.org/2003/05/soap-envelope'],
        ];
    }

    #[DataProvider('unprefixedEnvelopeProvider')]
    public function testUnprefixedEnvelopeIsDeserialized(string $soapNamespace): void
    {
        $this->downloadEnvelope($this->unprefixedEnvelope($soapNamespace));
    }

    #[DataProvider('unprefixedEnvelopeProvider')]
    public function testUnprefixedEnvelopeDoesNotEmitPhpWarnings(string $soapNamespace): void
    {
        /** @var string[] $errors */
        $errors = [];
        set_error_handler(static function (int $errno, string $message) use (&$errors): bool {
            $errors[] = $message;

            return true;
        });

        try {
            $this->downloadEnvelope($this->unprefixedEnvelope($soapNamespace));
        } finally {
            restore_error_handler();
        }

        self::assertSame([], $errors, 'An unprefixed SOAP envelope must not emit PHP warnings.');
    }

    private function unprefixedEnvelope(string $soapNamespace): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Envelope xmlns="{$soapNamespace}">
  <Body>
    <p:MessageEnvelopeDownloadResponse xmlns:p="http://isds.czechpoint.cz/v20">
      <p:dmStatus>
        <p:dmStatusCode>0000</p:dmStatusCode>
        <p:dmStatusMessage>Operation successfully</p:dmStatusMessage>
      </p:dmStatus>
    </p:MessageEnvelopeDownloadResponse>
  </Body>
</Envelope>
XML;
    }
}
