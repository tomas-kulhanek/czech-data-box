<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\DTO\Request\MessageEnvelopeDownload;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Exception\ConnectionException;
use TomasKulhanek\Tests\CzechDataBox\SerializerTrait;

class ConnectorXmlErrorTest extends TestCase
{
    use SerializerTrait;

    private function createAccount(): Account
    {
        $account = new Account();
        $account->setLoginName('login')
            ->setPassword('password')
            ->setLoginType(LoginTypeEnum::NAME_PASSWORD);

        return $account;
    }

    private function createRequest(): MessageEnvelopeDownload
    {
        $request = new MessageEnvelopeDownload();
        $request->setDataMessageId('123456789');

        return $request;
    }

    public function testMalformedResponseThrowsConnectionException(): void
    {
        $connector = new Connector(
            self::createSerializer(),
            new RecordingClientProvider('<soap:Envelope><soap:Body><p:Broken></soap:Envelope>')
        );

        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessageMatches('~could not be parsed~');

        $connector->messageEnvelopeDownload($this->createAccount(), $this->createRequest());
    }

    public function testMalformedResponseDoesNotEmitPhpWarnings(): void
    {
        $connector = new Connector(
            self::createSerializer(),
            new RecordingClientProvider('nejde o XML vůbec')
        );

        /** @var string[] $errors */
        $errors = [];
        set_error_handler(static function (int $errno, string $message) use (&$errors): bool {
            $errors[] = $message;

            return true;
        });

        try {
            $connector->messageEnvelopeDownload($this->createAccount(), $this->createRequest());
            self::fail('A malformed response must be rejected with a ConnectionException.');
        } catch (ConnectionException) {
        } finally {
            restore_error_handler();
        }

        self::assertSame([], $errors, 'Parsing a malformed response must not emit PHP warnings.');
    }

    public function testOversizedResponseIsRejectedBeforeParsing(): void
    {
        $provider = new RecordingClientProvider(str_repeat('a', 128));
        $connector = new Connector(self::createSerializer(), $provider, 64);

        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessageMatches('~larger than the allowed~');

        $connector->messageEnvelopeDownload($this->createAccount(), $this->createRequest());
    }

    public function testResponseWithinLimitIsAccepted(): void
    {
        $soapResponse = <<<XML
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
XML;
        $connector = new Connector(
            self::createSerializer(),
            new RecordingClientProvider($soapResponse),
            strlen($soapResponse)
        );

        $response = $connector->messageEnvelopeDownload($this->createAccount(), $this->createRequest());

        self::assertTrue($response->getStatus()->isOk());
    }

    public function testExternalEntityIsNotResolved(): void
    {
        $soapResponse = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE Envelope [
  <!ENTITY xxe SYSTEM "file:///etc/passwd">
]>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
  <SOAP-ENV:Body>
    <p:MessageEnvelopeDownloadResponse xmlns:p="http://isds.czechpoint.cz/v20">
      <p:dmStatus>
        <p:dmStatusCode>0000</p:dmStatusCode>
        <p:dmStatusMessage>&xxe;</p:dmStatusMessage>
      </p:dmStatus>
    </p:MessageEnvelopeDownloadResponse>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;
        $connector = new Connector(
            self::createSerializer(),
            new RecordingClientProvider($soapResponse)
        );

        try {
            $response = $connector->messageEnvelopeDownload($this->createAccount(), $this->createRequest());
        } catch (ConnectionException $exception) {
            self::assertStringNotContainsString(
                'root:',
                $exception->getMessage(),
                'An external entity must never be resolved.'
            );

            return;
        }

        self::fail(sprintf(
            'A response with an external entity must be rejected, the status message was "%s".',
            $response->getStatus()->getMessage()
        ));
    }

    public function testRecursiveEntityExpansionIsRejected(): void
    {
        $soapResponse = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE Envelope [
  <!ENTITY lol "lol">
  <!ENTITY lol1 "&lol;&lol;&lol;&lol;&lol;&lol;&lol;&lol;&lol;&lol;">
  <!ENTITY lol2 "&lol1;&lol1;&lol1;&lol1;&lol1;&lol1;&lol1;&lol1;&lol1;&lol1;">
  <!ENTITY lol3 "&lol2;&lol2;&lol2;&lol2;&lol2;&lol2;&lol2;&lol2;&lol2;&lol2;">
  <!ENTITY lol4 "&lol3;&lol3;&lol3;&lol3;&lol3;&lol3;&lol3;&lol3;&lol3;&lol3;">
  <!ENTITY lol5 "&lol4;&lol4;&lol4;&lol4;&lol4;&lol4;&lol4;&lol4;&lol4;&lol4;">
]>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
  <SOAP-ENV:Body>
    <p:MessageEnvelopeDownloadResponse xmlns:p="http://isds.czechpoint.cz/v20">
      <p:dmStatus>
        <p:dmStatusCode>0000</p:dmStatusCode>
        <p:dmStatusMessage>&lol5;</p:dmStatusMessage>
      </p:dmStatus>
    </p:MessageEnvelopeDownloadResponse>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;
        $connector = new Connector(
            self::createSerializer(),
            new RecordingClientProvider($soapResponse)
        );

        $this->expectException(ConnectionException::class);

        $connector->messageEnvelopeDownload($this->createAccount(), $this->createRequest());
    }
}
