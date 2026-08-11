<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\DTO\BigMessageEnvelope;
use TomasKulhanek\CzechDataBox\DTO\BigMessageFiles;
use TomasKulhanek\CzechDataBox\DTO\Envelope;
use TomasKulhanek\CzechDataBox\DTO\ExtFile;
use TomasKulhanek\CzechDataBox\DTO\File;
use TomasKulhanek\CzechDataBox\DTO\Recipient;
use TomasKulhanek\CzechDataBox\DTO\Request\CreateBigMessage;
use TomasKulhanek\CzechDataBox\DTO\Request\CreateMessage;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Exception\ConnectionException;
use TomasKulhanek\CzechDataBox\Exception\FieldLengthOverflow;
use TomasKulhanek\CzechDataBox\Provider\ClientProviderInterface;
use TomasKulhanek\Tests\CzechDataBox\SerializerTrait;

/**
 * Text length limits of the message envelope, taken from the gMessageEnvelopeSub
 * and tMultipleMessageEnvelopeSub groups of dmBaseTypes.xsd.
 */
class EnvelopeLengthValidationTest extends TestCase
{
    use SerializerTrait;

    /**
     * Connector that fails the test as soon as anything is sent to ISDS.
     */
    private function createConnector(): Connector
    {
        $provider = $this->createMock(ClientProviderInterface::class);
        $provider->expects(self::never())->method('sendRequest');

        return new Connector(self::createSerializer(), $provider);
    }

    /**
     * Connector whose provider proves the request passed validation — it is
     * reached only when no length check rejected the envelope.
     */
    private function createSendingConnector(): Connector
    {
        $provider = $this->createMock(ClientProviderInterface::class);
        $provider->expects(self::once())
            ->method('sendRequest')
            ->willThrowException(new ConnectionException('request has been sent'));

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

    private function createEnvelope(): Envelope
    {
        $envelope = new Envelope();
        $envelope->setAnnotation('Testovací zpráva');

        return $envelope;
    }

    private function createRequest(Envelope $envelope): CreateMessage
    {
        $recipient = new Recipient();
        $recipient->setDataBoxId('abcdefg');

        $mainFile = new File();
        $mainFile->setMimeType('application/pdf')
            ->setMetaType('main')
            ->setDescription('main.pdf')
            ->setXmlContent('obsah');

        $request = new CreateMessage();
        $request->setEnvelope($envelope)
            ->addRecipient($recipient)
            ->addFile($mainFile);

        return $request;
    }

    private function createBigMessageRequest(BigMessageEnvelope $envelope): CreateBigMessage
    {
        $extFile = new ExtFile();
        $extFile->setMetaType('main')
            ->setAttachmentId('ATT123')
            ->setAttachmentHash1('aaaa')
            ->setAttachmentHash1Algorithm('SHA-256')
            ->setAttachmentHash2('bbbb')
            ->setAttachmentHash2Algorithm('SHA-512');

        $files = new BigMessageFiles();
        $files->addExtFile($extFile);

        $request = new CreateBigMessage();
        $request->setEnvelope($envelope)
            ->setFiles($files);

        return $request;
    }

    private function createBigMessageEnvelope(): BigMessageEnvelope
    {
        $envelope = new BigMessageEnvelope();
        $envelope->setType('V')
            ->setRecipientId('abcdefg')
            ->setAnnotation('Testovací VoDZ');

        return $envelope;
    }

    public function testTooLongAnnotationIsRejected(): void
    {
        $envelope = $this->createEnvelope();
        $envelope->setAnnotation(str_repeat('a', Connector::MAX_ANNOTATION_LENGTH + 1));

        $this->expectException(FieldLengthOverflow::class);
        $this->expectExceptionMessage('dmAnnotation');
        $this->createConnector()->createMessage($this->createAccount(), $this->createRequest($envelope));
    }

    public function testTooLongSenderRefNumberIsRejected(): void
    {
        $envelope = $this->createEnvelope();
        $envelope->setSenderRefNumber(str_repeat('a', Connector::MAX_REF_NUMBER_LENGTH + 1));

        $this->expectException(FieldLengthOverflow::class);
        $this->expectExceptionMessage('dmSenderRefNumber');
        $this->createConnector()->createMessage($this->createAccount(), $this->createRequest($envelope));
    }

    public function testTooLongRecipientRefNumberIsRejected(): void
    {
        $envelope = $this->createEnvelope();
        $envelope->setRecipientRefNumber(str_repeat('a', Connector::MAX_REF_NUMBER_LENGTH + 1));

        $this->expectException(FieldLengthOverflow::class);
        $this->expectExceptionMessage('dmRecipientRefNumber');
        $this->createConnector()->createMessage($this->createAccount(), $this->createRequest($envelope));
    }

    public function testTooLongSenderIdentIsRejected(): void
    {
        $envelope = $this->createEnvelope();
        $envelope->setSenderIdent(str_repeat('a', Connector::MAX_IDENT_LENGTH + 1));

        $this->expectException(FieldLengthOverflow::class);
        $this->expectExceptionMessage('dmSenderIdent');
        $this->createConnector()->createMessage($this->createAccount(), $this->createRequest($envelope));
    }

    public function testTooLongRecipientIdentIsRejected(): void
    {
        $envelope = $this->createEnvelope();
        $envelope->setRecipientIdent(str_repeat('a', Connector::MAX_IDENT_LENGTH + 1));

        $this->expectException(FieldLengthOverflow::class);
        $this->expectExceptionMessage('dmRecipientIdent');
        $this->createConnector()->createMessage($this->createAccount(), $this->createRequest($envelope));
    }

    public function testFieldsAtTheLimitAreAccepted(): void
    {
        $envelope = $this->createEnvelope();
        $envelope->setAnnotation(str_repeat('a', Connector::MAX_ANNOTATION_LENGTH))
            ->setSenderRefNumber(str_repeat('a', Connector::MAX_REF_NUMBER_LENGTH))
            ->setRecipientRefNumber(str_repeat('a', Connector::MAX_REF_NUMBER_LENGTH))
            ->setSenderIdent(str_repeat('a', Connector::MAX_IDENT_LENGTH))
            ->setRecipientIdent(str_repeat('a', Connector::MAX_IDENT_LENGTH));

        $this->expectException(ConnectionException::class);
        $this->createSendingConnector()->createMessage($this->createAccount(), $this->createRequest($envelope));
    }

    public function testLengthIsCountedInCharactersNotBytes(): void
    {
        $envelope = $this->createEnvelope();
        $envelope->setAnnotation(str_repeat('ř', Connector::MAX_ANNOTATION_LENGTH));

        $this->expectException(ConnectionException::class);
        $this->createSendingConnector()->createMessage($this->createAccount(), $this->createRequest($envelope));
    }

    public function testBigMessageRejectsTooLongAnnotation(): void
    {
        $envelope = $this->createBigMessageEnvelope();
        $envelope->setAnnotation(str_repeat('a', Connector::MAX_ANNOTATION_LENGTH + 1));

        $this->expectException(FieldLengthOverflow::class);
        $this->expectExceptionMessage('dmAnnotation');
        $this->createConnector()->createBigMessage($this->createAccount(), $this->createBigMessageRequest($envelope));
    }

    public function testBigMessageRejectsTooLongSenderRefNumber(): void
    {
        $envelope = $this->createBigMessageEnvelope();
        $envelope->setSenderRefNumber(str_repeat('a', Connector::MAX_REF_NUMBER_LENGTH + 1));

        $this->expectException(FieldLengthOverflow::class);
        $this->expectExceptionMessage('dmSenderRefNumber');
        $this->createConnector()->createBigMessage($this->createAccount(), $this->createBigMessageRequest($envelope));
    }
}
