<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\DTO\BigAttachment;
use TomasKulhanek\CzechDataBox\DTO\BigMessageEnvelope;
use TomasKulhanek\CzechDataBox\DTO\BigMessageFiles;
use TomasKulhanek\CzechDataBox\DTO\ExtFile;
use TomasKulhanek\CzechDataBox\DTO\File;
use TomasKulhanek\CzechDataBox\DTO\Request\CreateBigMessage;
use TomasKulhanek\CzechDataBox\DTO\Request\UploadAttachment;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Exception\AttachmentCountOverflow;
use TomasKulhanek\CzechDataBox\Exception\DisallowedAttachmentFormat;
use TomasKulhanek\CzechDataBox\Exception\FileSizeOverflow;
use TomasKulhanek\CzechDataBox\Exception\MissingMainFile;
use TomasKulhanek\CzechDataBox\Exception\MissingRequiredField;
use TomasKulhanek\CzechDataBox\Provider\ClientProviderInterface;
use TomasKulhanek\CzechDataBox\Serializer\SplFileInfo;
use TomasKulhanek\Tests\CzechDataBox\SerializerTrait;

class VodzValidationTest extends TestCase
{
    use SerializerTrait;

    private function createConnector(): Connector
    {
        $provider = $this->createMock(ClientProviderInterface::class);
        $provider->expects(self::never())->method('sendRequest');

        return new Connector(self::createSerializer(), $provider);
    }

    private function createAccount(): Account
    {
        $account = new Account();
        $account->setLoginName('login')
            ->setPassword('password')
            ->setLoginType(LoginTypeEnum::NAME_PASSWORD);

        return $account;
    }

    private function createContent(string $content = 'obsah', ?int $reportedSize = null): SplFileInfo
    {
        if ($reportedSize === null) {
            return SplFileInfo::createInTemp($content);
        }

        $path = (string) tempnam(sys_get_temp_dir(), 'vodz');
        file_put_contents($path, $content);

        return new class ($path, $reportedSize) extends SplFileInfo {
            public function __construct(string $fileName, private readonly int $reportedSize)
            {
                parent::__construct($fileName, true);
            }

            public function getSize(): int
            {
                return $this->reportedSize;
            }
        };
    }

    private function createUploadRequest(
        string $description = 'priloha.pdf',
        ?SplFileInfo $content = null
    ): UploadAttachment {
        $attachment = new BigAttachment();
        $attachment->setMimeType('application/pdf')
            ->setDescription($description)
            ->setEncodedContent($content ?? $this->createContent());

        $request = new UploadAttachment();
        $request->setFile($attachment);

        return $request;
    }

    private function createExtFile(string $metaType = 'main'): ExtFile
    {
        $extFile = new ExtFile();
        $extFile->setMetaType($metaType)
            ->setAttachmentId('ATT123')
            ->setAttachmentHash1('aaaa')
            ->setAttachmentHash1Algorithm('SHA-256')
            ->setAttachmentHash2('bbbb')
            ->setAttachmentHash2Algorithm('SHA-512');

        return $extFile;
    }

    private function createInlineFile(string $description = 'priloha.txt', string $metaType = 'enclosure'): File
    {
        $file = new File();
        $file->setMimeType('text/plain')
            ->setMetaType($metaType)
            ->setDescription($description)
            ->setXmlContent('obsah');

        return $file;
    }

    private function createBigMessageRequest(BigMessageFiles $files): CreateBigMessage
    {
        $envelope = new BigMessageEnvelope();
        $envelope->setType('V');
        $envelope->setRecipientId('abcdefg');
        $envelope->setAnnotation('Testovací VoDZ');

        $request = new CreateBigMessage();
        $request->setEnvelope($envelope);
        $request->setFiles($files);

        return $request;
    }

    public function testUploadAttachmentRejectsDisallowedExtension(): void
    {
        $this->expectException(DisallowedAttachmentFormat::class);

        $this->createConnector()->uploadAttachment(
            $this->createAccount(),
            $this->createUploadRequest('malware.exe')
        );
    }

    public function testUploadAttachmentRejectsBlankDescription(): void
    {
        $this->expectException(MissingRequiredField::class);
        $this->expectExceptionMessage('dmFileDescr');

        $this->createConnector()->uploadAttachment(
            $this->createAccount(),
            $this->createUploadRequest('   ')
        );
    }

    public function testUploadAttachmentRejectsMissingContent(): void
    {
        $attachment = new BigAttachment();
        $attachment->setMimeType('application/pdf')
            ->setDescription('priloha.pdf');

        $request = new UploadAttachment();
        $request->setFile($attachment);

        $this->expectException(MissingRequiredField::class);
        $this->expectExceptionMessage('dmEncodedContent');

        $this->createConnector()->uploadAttachment($this->createAccount(), $request);
    }

    public function testUploadAttachmentRejectsOversizedFile(): void
    {
        $this->expectException(FileSizeOverflow::class);

        $this->createConnector()->uploadAttachment(
            $this->createAccount(),
            $this->createUploadRequest(
                'priloha.pdf',
                $this->createContent('obsah', Connector::MAX_BIG_MESSAGE_ATTACHMENTS_SIZE + 1)
            )
        );
    }

    public function testUploadAttachmentAcceptsValidAttachment(): void
    {
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

        $response = $connector->uploadAttachment(
            $this->createAccount(),
            $this->createUploadRequest(
                'priloha.pdf',
                $this->createContent('obsah', Connector::MAX_BIG_MESSAGE_ATTACHMENTS_SIZE)
            )
        );

        self::assertNotNull($provider->capturedBody);
        self::assertSame('ATT123456', $response->getAttachmentId());
        self::assertTrue($response->getStatus()->isOk());
    }

    public function testCreateBigMessageRequiresAtLeastOneExtFile(): void
    {
        $files = new BigMessageFiles();
        $files->addFile($this->createInlineFile('hlavni.pdf', 'main'));

        $this->expectException(MissingRequiredField::class);
        $this->expectExceptionMessage('dmExtFile');

        $this->createConnector()->createBigMessage(
            $this->createAccount(),
            $this->createBigMessageRequest($files)
        );
    }

    public function testCreateBigMessageRequiresMainAttachment(): void
    {
        $files = new BigMessageFiles();
        $files->addExtFile($this->createExtFile('enclosure'));
        $files->addFile($this->createInlineFile());

        $this->expectException(MissingMainFile::class);

        $this->createConnector()->createBigMessage(
            $this->createAccount(),
            $this->createBigMessageRequest($files)
        );
    }

    public function testCreateBigMessageAcceptsMainAttachmentInInlineFiles(): void
    {
        $files = new BigMessageFiles();
        $files->addExtFile($this->createExtFile('enclosure'));
        $files->addFile($this->createInlineFile('hlavni.pdf', 'main'));

        $provider = new RecordingClientProvider($this->createBigMessageResponse());
        $connector = new Connector(self::createSerializer(), $provider);

        $response = $connector->createBigMessage(
            $this->createAccount(),
            $this->createBigMessageRequest($files)
        );

        self::assertNotNull($provider->capturedBody);
        self::assertTrue($response->getStatus()->isOk());
    }

    public function testCreateBigMessageRejectsTooManyAttachments(): void
    {
        $files = new BigMessageFiles();
        $files->addExtFile($this->createExtFile());
        for ($i = 0; $i < Connector::MAX_ATTACHMENT_COUNT; $i++) {
            $files->addFile($this->createInlineFile());
        }

        $this->expectException(AttachmentCountOverflow::class);

        $this->createConnector()->createBigMessage(
            $this->createAccount(),
            $this->createBigMessageRequest($files)
        );
    }

    public function testCreateBigMessageRejectsDisallowedInlineFormat(): void
    {
        $files = new BigMessageFiles();
        $files->addExtFile($this->createExtFile());
        $files->addFile($this->createInlineFile('malware.exe'));

        $this->expectException(DisallowedAttachmentFormat::class);

        $this->createConnector()->createBigMessage(
            $this->createAccount(),
            $this->createBigMessageRequest($files)
        );
    }

    public function testCreateBigMessageRejectsOversizedInlineFiles(): void
    {
        $file = new File();
        $file->setMimeType('application/pdf')
            ->setMetaType('main')
            ->setDescription('hlavni.pdf')
            ->setEncodedContent($this->createContent('obsah', Connector::MAX_BIG_MESSAGE_ATTACHMENTS_SIZE + 1));

        $files = new BigMessageFiles();
        $files->addExtFile($this->createExtFile());
        $files->addFile($file);

        $this->expectException(FileSizeOverflow::class);

        $this->createConnector()->createBigMessage(
            $this->createAccount(),
            $this->createBigMessageRequest($files)
        );
    }

    public function testCreateBigMessageRequiresRecipient(): void
    {
        $files = new BigMessageFiles();
        $files->addExtFile($this->createExtFile());

        $request = $this->createBigMessageRequest($files);
        $request->getEnvelope()->setRecipientId(null);

        $this->expectException(MissingRequiredField::class);
        $this->expectExceptionMessage('dbIDRecipient');

        $this->createConnector()->createBigMessage($this->createAccount(), $request);
    }

    public function testCreateBigMessageRequiresAnnotation(): void
    {
        $files = new BigMessageFiles();
        $files->addExtFile($this->createExtFile());

        $request = $this->createBigMessageRequest($files);
        $request->getEnvelope()->setAnnotation(null);

        $this->expectException(MissingRequiredField::class);
        $this->expectExceptionMessage('annotation');

        $this->createConnector()->createBigMessage($this->createAccount(), $request);
    }

    private function createBigMessageResponse(): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
  <soap:Body>
    <p:CreateBigMessageResponse xmlns:p="http://isds.czechpoint.cz/v20">
      <p:dmStatus>
        <p:dmStatusCode>0000</p:dmStatusCode>
        <p:dmStatusMessage>Operation successfully</p:dmStatusMessage>
      </p:dmStatus>
    </p:CreateBigMessageResponse>
  </soap:Body>
</soap:Envelope>
XML;
    }
}
