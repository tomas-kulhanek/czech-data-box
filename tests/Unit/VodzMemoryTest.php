<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\DTO\BigAttachment;
use TomasKulhanek\CzechDataBox\DTO\Request\UploadAttachment;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Serializer\SplFileInfo;
use TomasKulhanek\Tests\CzechDataBox\SerializerTrait;

class VodzMemoryTest extends TestCase
{
    use SerializerTrait;

    private const int CHUNK_SIZE = 1024 * 1024;

    private const int ATTACHMENT_SIZE = 32 * self::CHUNK_SIZE;

    private const int BASELINE_ALLOWANCE = 64 * 1024 * 1024;

    #[Group('memory')]
    #[RunInSeparateProcess]
    public function testUploadAttachmentPeakMemoryStaysBounded(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'vodz_mem');
        self::assertNotFalse($path);

        try {
            $handle = fopen($path, 'wb');
            self::assertNotFalse($handle);
            $chunk = str_repeat('a', self::CHUNK_SIZE);
            for ($written = 0; $written < self::ATTACHMENT_SIZE; $written += self::CHUNK_SIZE) {
                fwrite($handle, $chunk);
            }
            fclose($handle);
            unset($chunk);

            $soapResponse = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
  <soap:Body>
    <p:UploadAttachmentResponse xmlns:p="http://isds.czechpoint.cz/v20">
      <p:dmAttID>ATT123456</p:dmAttID>
      <p:dmStatus>
        <p:dmStatusCode>0000</p:dmStatusCode>
        <p:dmStatusMessage>Operation successfully</p:dmStatusMessage>
      </p:dmStatus>
    </p:UploadAttachmentResponse>
  </soap:Body>
</soap:Envelope>
XML;
            $provider = new RecordingClientProvider($soapResponse);
            $connector = new Connector(self::createSerializer(), $provider);

            $account = new Account();
            $account->setLoginName('login')
                ->setPassword('password')
                ->setLoginType(LoginTypeEnum::NAME_PASSWORD);

            $attachment = new BigAttachment();
            $attachment->setMimeType('application/pdf')
                ->setDescription('priloha.pdf')
                ->setEncodedContent(new SplFileInfo($path, true));

            $request = new UploadAttachment();
            $request->setFile($attachment);

            $response = $connector->uploadAttachment($account, $request);

            self::assertTrue($response->getStatus()->isOk());
            self::assertNotNull($provider->capturedBody);
            self::assertStringContainsString('http://www.w3.org/2003/05/soap-envelope', $provider->capturedBody);
            self::assertLessThan(
                3 * self::ATTACHMENT_SIZE + self::BASELINE_ALLOWANCE,
                memory_get_peak_usage(true),
                'Uploading a VoDZ attachment must not need more than 3x its size plus a fixed baseline.'
            );
        } finally {
            @unlink($path);
        }
    }
}
