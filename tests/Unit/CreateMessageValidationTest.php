<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\DTO\File;
use TomasKulhanek\CzechDataBox\DTO\Recipient;
use TomasKulhanek\CzechDataBox\DTO\Request\CreateMessage;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Exception\AttachmentCountOverflow;
use TomasKulhanek\CzechDataBox\Exception\DisallowedAttachmentFormat;
use TomasKulhanek\CzechDataBox\Exception\FileSizeOverflow;
use TomasKulhanek\CzechDataBox\Exception\RecipientCountOverflow;
use TomasKulhanek\CzechDataBox\Provider\ClientProviderInterface;
use TomasKulhanek\Serializer\SerializerFactory;

class CreateMessageValidationTest extends TestCase
{
    private function createConnector(): Connector
    {
        $provider = $this->createMock(ClientProviderInterface::class);
        $provider->expects(self::never())->method('sendRequest');
        return new Connector(SerializerFactory::create(), $provider);
    }

    private function createAccount(): Account
    {
        $account = new Account();
        $account->setLoginName('login')
            ->setPassword('password')
            ->setLoginType(LoginTypeEnum::NAME_PASSWORD)
            ->setProduction(false);
        return $account;
    }

    private function createFile(string $description, ?string $xmlContent = null): File
    {
        $file = new File();
        $file->setMimeType('application/octet-stream')
            ->setMetaType('enclosure')
            ->setDescription($description);
        if ($xmlContent !== null) {
            $file->setXmlContent($xmlContent);
        }
        return $file;
    }

    private function createRequest(): CreateMessage
    {
        $request = new CreateMessage();
        $recipient = new Recipient();
        $recipient->setDataBoxId('abcdefg');
        $request->addRecipient($recipient);
        return $request;
    }

    public function testDisallowedAttachmentFormatIsRejected(): void
    {
        $request = $this->createRequest();
        $request->addFile($this->createFile('malware.exe'));

        $this->expectException(DisallowedAttachmentFormat::class);
        $this->createConnector()->createMessage($this->createAccount(), $request);
    }

    public function testMoreThanHundredAttachmentsAreRejected(): void
    {
        $request = $this->createRequest();
        for ($i = 1; $i <= 101; $i++) {
            $request->addFile($this->createFile(sprintf('file%d.pdf', $i)));
        }

        $this->expectException(AttachmentCountOverflow::class);
        $this->createConnector()->createMessage($this->createAccount(), $request);
    }

    public function testMoreThanTenContainerAttachmentsAreRejected(): void
    {
        $request = $this->createRequest();
        for ($i = 1; $i <= 11; $i++) {
            $request->addFile($this->createFile(sprintf('archive%d.zip', $i)));
        }

        $this->expectException(AttachmentCountOverflow::class);
        $this->createConnector()->createMessage($this->createAccount(), $request);
    }

    public function testXmlContentCountsTowardsSizeLimit(): void
    {
        $request = $this->createRequest();
        $request->addFile($this->createFile('data.xml', str_repeat('a', 21 * 1024 ** 2)));

        $this->expectException(FileSizeOverflow::class);
        $this->createConnector()->createMessage($this->createAccount(), $request);
    }

    public function testMoreThanFiftyRecipientsAreRejected(): void
    {
        $request = new CreateMessage();
        for ($i = 1; $i <= 51; $i++) {
            $recipient = new Recipient();
            $recipient->setDataBoxId(sprintf('box%04d', $i));
            $request->addRecipient($recipient);
        }
        $request->addFile($this->createFile('main.pdf'));

        $this->expectException(RecipientCountOverflow::class);
        $this->createConnector()->createMessage($this->createAccount(), $request);
    }
}
