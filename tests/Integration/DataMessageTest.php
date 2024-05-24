<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Integration;

use TomasKulhanek\CzechDataBox\DTO\Request\AuthenticateMessage;
use TomasKulhanek\CzechDataBox\DTO\Request\VerifyMessage;
use TomasKulhanek\CzechDataBox\DTO\Recipient;
use TomasKulhanek\CzechDataBox\DTO\Request\GetListOfReceivedMessages;
use TomasKulhanek\CzechDataBox\Enum\FilterEnum;
use TomasKulhanek\CzechDataBox\Utils\MessageStatus;
use TomasKulhanek\CzechDataBox\DTO\Request\MarkMessageAsDownloaded;
use TomasKulhanek\CzechDataBox\DTO\Request\MessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Request\SignedMessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Request\GetListOfSentMessages;
use TomasKulhanek\CzechDataBox\DTO\Request\SignedSentMessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Request\ResignISDSDocument;
use TomasKulhanek\CzechDataBox\DTO\Request\MessageEnvelopeDownload;
use TomasKulhanek\CzechDataBox\DTO\Request\GetDeliveryInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\GetSignedDeliveryInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\GetMessageStateChanges;
use TomasKulhanek\CzechDataBox\DTO\Request\CreateMessage;
use TomasKulhanek\CzechDataBox\DTO\Envelope;
use TomasKulhanek\CzechDataBox\DTO\File;
use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\DTO;
use TomasKulhanek\CzechDataBox\Utils;
use TomasKulhanek\Serializer\Utils\SplFileInfo;

class DataMessageTest extends TestCase
{
    use AccountTrait;
    use ConnectorTrait;

    public function testAuthenticateMessage(): void
    {
        $client = $this->createGuzzleConnector();
        $message = new AuthenticateMessage();
        $message->setDataMessage($this->getOriginalMessage());
        $account = $this->createFOAccount();
        $response = $client->authenticateMessage($account, $message);
        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());
        self::assertFalse($response->isAuthenticated());
    }

    public function testVerifyMessage(): void
    {
        $client = $this->createGuzzleConnector();
        $account = $this->createFOAccount();
        $verifyMessage = new VerifyMessage();
        $verifyMessage->setDataMessageId('7903783');
        $response = $client->verifyMessage($account, $verifyMessage);
        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());
    }

    public function testCreateMessage(): void
    {
        self::assertTrue($this->createOVMAccount()->getDataBoxId() !== null);
        $newMessageEnvelope = $this->getNewMessageEnvelope();
        $recipient = new Recipient();
        $recipient->setDataBoxId($this->createOVMAccount()->getDataBoxId());
        $newMessageEnvelope->addRecipient($recipient);
        $response = $this->createGuzzleConnector()->createMessage($this->createFOAccount(), $newMessageEnvelope);

        self::assertTrue($response->isOk(), $response->getStatus()->getMessage());
    }

    public function testMessageDownload(): void
    {
        $ovmAccount = $this->createOvmCertAccount();
        $client = $this->createGuzzleConnector();

        $listrec = new GetListOfReceivedMessages();
        $listrec->setStatusFilter(FilterEnum::ALL)
            ->setListTo(new \DateTimeImmutable())
            ->setListFrom((new \DateTimeImmutable())->modify('-30 day'));

        $listrecRes = $client->getListOfReceivedMessages($ovmAccount, $listrec);
        self::assertTrue($listrecRes->getStatus()->isOk(), $listrecRes->getStatus()->getMessage());

        $messageId = $listrecRes->getRecord()[0]->getDataMessageId();

        $message = new MarkMessageAsDownloaded();
        $message->setDataMessageId($messageId);
        $response = $client->markMessageAsDownloaded($ovmAccount, $message);
        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());

        $request = new MessageDownload();
        $request->setDataMessageId($messageId);

        $response = $client->messageDownload($ovmAccount, $request);

        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());
        self::assertTrue($response->getReturnedMessage() !== null);
        self::assertSame($ovmAccount->getDataBoxId(), $response->getReturnedMessage()->getDataMessage()->getRecipientId());
    }

    public function testSignedMessageDownload(): void
    {
        $ovmAccount = $this->createOvmCertAccount();
        $client = $this->createGuzzleConnector();

        $listrec = new GetListOfReceivedMessages();
        $listrec->setStatusFilter(FilterEnum::ALL)
            ->setListTo(new \DateTimeImmutable())
            ->setListFrom((new \DateTimeImmutable())->modify('-30 day'));

        $listrecRes = $client->getListOfReceivedMessages($ovmAccount, $listrec);
        self::assertTrue($listrecRes->getStatus()->isOk(), $listrecRes->getStatus()->getMessage());

        $client = $this->createGuzzleConnector();
        $message = new MarkMessageAsDownloaded();
        $message->setDataMessageId($listrecRes->getRecord()[0]->getDataMessageId());
        $response = $client->markMessageAsDownloaded($ovmAccount, $message);
        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());

        $request = new SignedMessageDownload();
        $request->setDataMessageId($listrecRes->getRecord()[0]->getDataMessageId());

        $response = $client->signedMessageDownload($ovmAccount, $request);

        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());
    }

    public function testSignedSentMessageDownload(): void
    {
        $account = $this->createFOAccount();
        $client = $this->createGuzzleConnector();

        $listrec = new GetListOfSentMessages();
        $listrec->setStatusFilter(FilterEnum::ALL)
            ->setListTo(new \DateTimeImmutable())
            ->setListFrom((new \DateTimeImmutable())->modify('-30 day'));

        $listrecRes = $client->getListOfSentMessages($account, $listrec);
        self::assertTrue($listrecRes->getStatus()->isOk(), $listrecRes->getStatus()->getMessage());

        $client = $this->createGuzzleConnector();

        $request = new SignedSentMessageDownload();
        $request->setDataMessageId($listrecRes->getRecord()[0]->getDataMessageId());

        $response = $client->signedSentMessageDownload($account, $request);

        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());
    }

    public function testResignIsdsDocument(): void
    {
        $ovmAccount = $this->createFOAccount();
        $client = $this->createGuzzleConnector();

        $request = new ResignISDSDocument();
        $request->setDocument($this->getOriginalMessage());

        $response = $client->resignIsdsDocument($ovmAccount, $request);
        self::assertFalse($response->getStatus()->isOk(), $response->getStatus()->getMessage());
    }

    public function testMessageEnvelopeDownload(): void
    {
        $account = $this->createOvmCertAccount();
        $client = $this->createGuzzleConnector();

        $listrec = new GetListOfReceivedMessages();
        $listrec->setStatusFilter(FilterEnum::ALL)
            ->setListTo(new \DateTimeImmutable())
            ->setListFrom((new \DateTimeImmutable())->modify('-30 day'));

        $listrecRes = $client->getListOfReceivedMessages($account, $listrec);
        self::assertTrue($listrecRes->getStatus()->isOk(), $listrecRes->getStatus()->getMessage());

        $client = $this->createGuzzleConnector();

        $request = new MessageEnvelopeDownload();
        $request->setDataMessageId($listrecRes->getRecord()[0]->getDataMessageId());

        $response = $client->messageEnvelopeDownload($account, $request);

        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());
    }

    public function testMarkMessageAsDownloaded(): void
    {
        $account = $this->createOvmCertAccount();
        $client = $this->createGuzzleConnector();

        $listrec = new GetListOfReceivedMessages();
        $listrec->setStatusFilter(FilterEnum::ALL)
            ->setListTo(new \DateTimeImmutable())
            ->setListFrom((new \DateTimeImmutable())->modify('-30 day'));

        $listrecRes = $client->getListOfReceivedMessages($account, $listrec);
        self::assertTrue($listrecRes->getStatus()->isOk(), $listrecRes->getStatus()->getMessage());

        $client = $this->createGuzzleConnector();

        $request = new MarkMessageAsDownloaded();
        $request->setDataMessageId($listrecRes->getRecord()[0]->getDataMessageId());

        $response = $client->markMessageAsDownloaded($account, $request);

        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());
    }

    public function testDeliveryInfo(): void
    {
        $account = $this->createFOAccount();
        $client = $this->createGuzzleConnector();

        $listrec = new GetListOfSentMessages();
        $listrec->setStatusFilter(FilterEnum::ALL)
            ->setListTo(new \DateTimeImmutable())
            ->setListFrom((new \DateTimeImmutable())->modify('-30 day'));

        $listrecRes = $client->getListOfSentMessages($account, $listrec);
        self::assertTrue($listrecRes->getStatus()->isOk(), $listrecRes->getStatus()->getMessage());

        $client = $this->createGuzzleConnector();

        $request = new GetDeliveryInfo();
        $request->setDataMessageId($listrecRes->getRecord()[0]->getDataMessageId());

        $response = $client->getDeliveryInfo($account, $request);

        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());
    }

    public function testSignedDeliveryInfo(): void
    {
        $account = $this->createFOAccount();
        $client = $this->createGuzzleConnector();

        $listrec = new GetListOfSentMessages();
        $listrec->setStatusFilter(FilterEnum::ALL)
            ->setListTo(new \DateTimeImmutable())
            ->setListFrom((new \DateTimeImmutable())->modify('-30 day'));

        $listrecRes = $client->getListOfSentMessages($account, $listrec);
        self::assertTrue($listrecRes->getStatus()->isOk(), $listrecRes->getStatus()->getMessage());

        $client = $this->createGuzzleConnector();

        $request = new GetSignedDeliveryInfo();
        $request->setDataMessageId($listrecRes->getRecord()[0]->getDataMessageId());

        $response = $client->getSignedDeliveryInfo($account, $request);

        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());
    }

    public function testMessageStateChanges(): void
    {
        $account = $this->createOvmCertAccount();
        $client = $this->createGuzzleConnector();

        $request = new GetMessageStateChanges();
        $request
            ->setChangesTo(new \DateTimeImmutable())
            ->setChangesFrom((new \DateTimeImmutable())->modify('-30 day'));

        $response = $client->getMessageStateChanges($account, $request);

        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());
    }

    private function getNewMessageEnvelope(): CreateMessage
    {
        $createMessage = new CreateMessage();
        $createMessage
            ->setEnvelope((new Envelope()));
        $createMessage->getEnvelope()
            ->setPersonalDelivery(false)
            ->setOvm(false)
            ->setLegalTitleSect('legalTitleSect')
            ->setLegalTitlePoint('legalTitlePoint')
            ->setLegalTitlePar('legalTitlePar')
            ->setLegalTitleLaw(666)
            ->setAllowSubstDelivery(true)
            ->setAnnotation('Test message - ' . date('Y-m-d'))
            ->setLegalTitleYear((int) date('Y'))
            ->setPublishOwnId(false)
            ->setRecipientIdent('recipientIdent')
            ->setRecipientOrgUnit('recipientOrgUnit')
            ->setRecipientOrgUnitNum(777)
            ->setRecipientRefNumber('recipientRefNumber')
            ->setSenderIdent('senderIdent')
            ->setSenderOrgUnit('senderOrgUnit')
            ->setSenderOrgUnitNum(999)
            ->setSenderRefNumber('senderRefNumber')
            ->setType('type');

        $file = new File();
        $file->setEncodedContent($this->getTestAttachment())
            ->setFormat('pdf')
            ->setDescription('example.pdf')
            ->setMimeType('application/pdf')
            ->setMetaType('main');
        $createMessage->addFile($file);

        return $createMessage;
    }

    private function getTestAttachment(): SplFileInfo
    {
        return SplFileInfo::createFromSplFileInfo(
            new \SplFileInfo(__DIR__ . '/../_data/attachment.pdf')
        );
    }

    private function getOriginalMessage(): SplFileInfo
    {
        return SplFileInfo::createFromSplFileInfo(
            new \SplFileInfo(__DIR__ . '/../_data/fo_inbox.zfo')
        );
    }
}
