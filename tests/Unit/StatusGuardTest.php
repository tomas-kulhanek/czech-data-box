<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\DTO\DataMessageStatus;
use TomasKulhanek\CzechDataBox\DTO\Response\CreateMessage;
use TomasKulhanek\CzechDataBox\Exception\CzechDataBoxException;
use TomasKulhanek\CzechDataBox\Exception\IsdsStatusError;
use TomasKulhanek\CzechDataBox\Utils\StatusGuard;
use TomasKulhanek\Tests\CzechDataBox\SerializerTrait;

class StatusGuardTest extends TestCase
{
    use SerializerTrait;

    private function createStatus(string $code, string $message, ?string $refNumber = null): DataMessageStatus
    {
        $status = new DataMessageStatus();
        $status->setCode($code);
        $status->setMessage($message);
        $status->setRefNumber($refNumber);
        return $status;
    }

    public function testOkStatusPasses(): void
    {
        StatusGuard::assertStatusOk($this->createStatus('0000', 'Ok'));
        StatusGuard::assertStatusOk($this->createStatus('0004', 'Partial success'));
        $this->addToAssertionCount(1);
    }

    public function testNonOkStatusThrowsWithDetails(): void
    {
        try {
            StatusGuard::assertStatusOk($this->createStatus('1220', 'Wrong message format', 'REF-123'));
            self::fail('Expected IsdsStatusError to be thrown');
        } catch (IsdsStatusError $exception) {
            self::assertSame('1220', $exception->statusCode);
            self::assertSame('Wrong message format', $exception->statusMessage);
            self::assertSame('REF-123', $exception->refNumber);
            self::assertStringContainsString('1220', $exception->getMessage());
            self::assertStringContainsString('Wrong message format', $exception->getMessage());
        }
    }

    public function testKnownCodeMessageContainsCzechHint(): void
    {
        try {
            StatusGuard::assertStatusOk($this->createStatus('1281', 'Message is VoDZ'));
            self::fail('Expected IsdsStatusError to be thrown');
        } catch (IsdsStatusError $exception) {
            self::assertStringContainsString('VoDZ operace na ws2', $exception->getMessage());
        }

        try {
            StatusGuard::assertStatusOk($this->createStatus('1201', 'Box is not accessible'));
            self::fail('Expected IsdsStatusError to be thrown');
        } catch (IsdsStatusError $exception) {
            self::assertStringContainsString('znepřístupněna', $exception->getMessage());
        }

        try {
            StatusGuard::assertStatusOk($this->createStatus('2046', 'Antivirus timeout'));
            self::fail('Expected IsdsStatusError to be thrown');
        } catch (IsdsStatusError $exception) {
            self::assertStringContainsString('antivirová kontrola nedoběhla', $exception->getMessage());
        }
    }

    public function testAssertOkUsesResponseStatus(): void
    {
        $response = new CreateMessage();
        $response->setStatus($this->createStatus('0000', 'Ok'));
        StatusGuard::assertOk($response);
        $this->addToAssertionCount(1);

        $response->setStatus($this->createStatus('1201', 'Box is not accessible'));
        $this->expectException(IsdsStatusError::class);
        StatusGuard::assertOk($response);
    }

    public function testAssertOkChecksCreateMessageMultipleStatus(): void
    {
        $xml = <<<XML_WRAP
<?xml version="1.0" encoding="UTF-8"?>
<p:CreateMultipleMessageResponse xmlns:p="https://isds.czechpoint.cz/v20">
  <p:dmMultipleStatus>
    <p:dmSingleStatus>
      <p:dmStatus>
        <p:dmStatusCode>0000</p:dmStatusCode>
        <p:dmStatusMessage>Ok.</p:dmStatusMessage>
      </p:dmStatus>
      <p:dmID>111</p:dmID>
    </p:dmSingleStatus>
    <p:dmSingleStatus>
      <p:dmStatus>
        <p:dmStatusCode>2046</p:dmStatusCode>
        <p:dmStatusMessage>Antivirus timeout</p:dmStatusMessage>
        <p:dmStatusRefNumber>REF-999</p:dmStatusRefNumber>
      </p:dmStatus>
      <p:dmID>222</p:dmID>
    </p:dmSingleStatus>
  </p:dmMultipleStatus>
  <p:dmStatus>
    <p:dmStatusCode>0004</p:dmStatusCode>
    <p:dmStatusMessage>Partial success</p:dmStatusMessage>
  </p:dmStatus>
</p:CreateMultipleMessageResponse>
XML_WRAP;
        $response = self::deserializeXml($xml, CreateMessage::class);
        self::assertCount(2, $response->getMultipleStatus());
        self::assertFalse($response->isOk());

        try {
            StatusGuard::assertOk($response);
            self::fail('Expected IsdsStatusError to be thrown');
        } catch (IsdsStatusError $exception) {
            self::assertSame('2046', $exception->statusCode);
            self::assertSame('REF-999', $exception->refNumber);
        }
    }

    public function testIsdsStatusErrorImplementsCzechDataBoxException(): void
    {
        $exception = new IsdsStatusError('1220', 'Wrong message format');
        self::assertInstanceOf(CzechDataBoxException::class, $exception);
    }
}
