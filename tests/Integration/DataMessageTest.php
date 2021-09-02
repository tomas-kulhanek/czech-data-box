<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Integration;

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
        $client = $this->createConnector();
        $message = new DTO\Request\AuthenticateMessage();
        $message->setDataMessage($this->getOriginalMessage());
        $account = $this->createFOAccount();
        $response = $client->authenticateMessage($account, $message);
        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());
        self::assertTrue($response->isAuthenticated());
    }

    public function testVerifyMessage(): void
    {
        $client = $this->createConnector();
        $account = $this->createFOAccount();
        $verifyMessage = new DTO\Request\VerifyMessage();
        $verifyMessage->setDataMessageId('7903783');
        $response = $client->verifyMessage($account, $verifyMessage);
        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());
    }

    public function testCreateMessage(): void
    {
        $newMessageEnvelope = $this->getNewMessageEnvelope();
        $recipient = new DTO\Recipient();
        $recipient->setDataBoxId($this->createOVMAccount()->getDataBoxId());
        $newMessageEnvelope->addRecipient($recipient);
        $response = $this->createConnector()->createMessage($this->createFOAccount(), $newMessageEnvelope);

        self::assertTrue($response->isOk(), $response->getStatus()->getMessage());
    }

    public function testMessageDownload(): void
    {
        $ovmAccount = $this->createOVMAccount();
        $client = $this->createConnector();

        $listrec = new DTO\Request\GetListOfReceivedMessages();
        $listrec->setStatusFilter(Utils\MessageStatus::getDecEntryForStatus(Utils\MessageStatus::FILTER_ALL))
            ->setListTo(new \DateTimeImmutable())
            ->setListFrom((new \DateTimeImmutable())->modify('-1 day'));

        $listrecRes = $client->getListOfReceivedMessages($ovmAccount, $listrec);
        self::assertTrue($listrecRes->getStatus()->isOk(), $listrecRes->getStatus()->getMessage());

        $client = $this->createConnector();
        $message = new DTO\Request\MarkMessageAsDownloaded();
        $message->setDataMessageId($listrecRes->getRecord()[0]->getDataMessageId());
        $response = $client->markMessageAsDownloaded($ovmAccount, $message);
        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());

        $request = new DTO\Request\MessageDownload();
        $request->setDataMessageId($listrecRes->getRecord()[0]->getDataMessageId());

        $response = $client->messageDownload($ovmAccount, $request);

        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());
        self::assertSame($ovmAccount->getDataBoxId(), $response->getReturnedMessage()->getDataMessage()->getRecipientId());
    }

    public function testSignedMessageDownload(): void
    {
        $ovmAccount = $this->createOVMAccount();
        $client = $this->createConnector();

        $listrec = new DTO\Request\GetListOfReceivedMessages();
        $listrec->setStatusFilter(Utils\MessageStatus::getDecEntryForStatus(Utils\MessageStatus::FILTER_ALL))
            ->setListTo(new \DateTimeImmutable())
            ->setListFrom((new \DateTimeImmutable())->modify('-1 day'));

        $listrecRes = $client->getListOfReceivedMessages($ovmAccount, $listrec);
        self::assertTrue($listrecRes->getStatus()->isOk(), $listrecRes->getStatus()->getMessage());

        $client = $this->createConnector();
        $message = new DTO\Request\MarkMessageAsDownloaded();
        $message->setDataMessageId($listrecRes->getRecord()[0]->getDataMessageId());
        $response = $client->markMessageAsDownloaded($ovmAccount, $message);
        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());

        $request = new DTO\Request\SignedMessageDownload();
        $request->setDataMessageId($listrecRes->getRecord()[0]->getDataMessageId());

        $response = $client->signedMessageDownload($ovmAccount, $request);

        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());
    }

    public function testSignedSentMessageDownload(): void
    {
        $account = $this->createFOAccount();
        $client = $this->createConnector();

        $listrec = new DTO\Request\GetListOfSentMessages();
        $listrec->setStatusFilter(Utils\MessageStatus::getDecEntryForStatus(Utils\MessageStatus::FILTER_ALL))
            ->setListTo(new \DateTimeImmutable())
            ->setListFrom((new \DateTimeImmutable())->modify('-1 day'));

        $listrecRes = $client->getListOfSentMessages($account, $listrec);
        self::assertTrue($listrecRes->getStatus()->isOk(), $listrecRes->getStatus()->getMessage());

        $client = $this->createConnector();

        $request = new DTO\Request\SignedSentMessageDownload();
        $request->setDataMessageId($listrecRes->getRecord()[0]->getDataMessageId());

        $response = $client->signedSentMessageDownload($account, $request);

        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());
    }

    public function testResignIsdsDocument(): void
    {
        $ovmAccount = $this->createFOAccount();
        $client = $this->createConnector();

        $request = new DTO\Request\ResignISDSDocument();
        $request->setDocument($this->getOriginalMessage());

        $response = $client->resignIsdsDocument($ovmAccount, $request);
        self::assertFalse($response->getStatus()->isOk(), $response->getStatus()->getMessage());
        self::assertSame($response->getStatus()->getMessage(), 'Nejsou splněny podmínky pro provedení re-autorizace, dokument je již v alespoň CAdES-EPES.');
    }

    public function testMessageEnvelopeDownload(): void
    {
        $account = $this->createOVMAccount();
        $client = $this->createConnector();

        $listrec = new DTO\Request\GetListOfReceivedMessages();
        $listrec->setStatusFilter(Utils\MessageStatus::getDecEntryForStatus(Utils\MessageStatus::FILTER_ALL))
            ->setListTo(new \DateTimeImmutable())
            ->setListFrom((new \DateTimeImmutable())->modify('-1 day'));

        $listrecRes = $client->getListOfReceivedMessages($account, $listrec);
        self::assertTrue($listrecRes->getStatus()->isOk(), $listrecRes->getStatus()->getMessage());

        $client = $this->createConnector();

        $request = new DTO\Request\MessageEnvelopeDownload();
        $request->setDataMessageId($listrecRes->getRecord()[0]->getDataMessageId());

        $response = $client->messageEnvelopeDownload($account, $request);

        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());
    }

    public function testMarkMessageAsDownloaded(): void
    {
        $account = $this->createOVMAccount();
        $client = $this->createConnector();

        $listrec = new DTO\Request\GetListOfReceivedMessages();
        $listrec->setStatusFilter(Utils\MessageStatus::getDecEntryForStatus(Utils\MessageStatus::FILTER_ALL))
            ->setListTo(new \DateTimeImmutable())
            ->setListFrom((new \DateTimeImmutable())->modify('-1 day'));

        $listrecRes = $client->getListOfReceivedMessages($account, $listrec);
        self::assertTrue($listrecRes->getStatus()->isOk(), $listrecRes->getStatus()->getMessage());

        $client = $this->createConnector();

        $request = new DTO\Request\MarkMessageAsDownloaded();
        $request->setDataMessageId($listrecRes->getRecord()[0]->getDataMessageId());

        $response = $client->markMessageAsDownloaded($account, $request);

        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());
    }

    public function testDeliveryInfo(): void
    {
        $account = $this->createFOAccount();
        $client = $this->createConnector();

        $listrec = new DTO\Request\GetListOfSentMessages();
        $listrec->setStatusFilter(Utils\MessageStatus::getDecEntryForStatus(Utils\MessageStatus::FILTER_ALL))
            ->setListTo(new \DateTimeImmutable())
            ->setListFrom((new \DateTimeImmutable())->modify('-1 day'));

        $listrecRes = $client->getListOfSentMessages($account, $listrec);
        self::assertTrue($listrecRes->getStatus()->isOk(), $listrecRes->getStatus()->getMessage());

        $client = $this->createConnector();

        $request = new DTO\Request\GetDeliveryInfo();
        $request->setDataMessageId($listrecRes->getRecord()[0]->getDataMessageId());

        $response = $client->getDeliveryInfo($account, $request);

        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());
    }

    public function testSignedDeliveryInfo(): void
    {
        $account = $this->createFOAccount();
        $client = $this->createConnector();

        $listrec = new DTO\Request\GetListOfSentMessages();
        $listrec->setStatusFilter(Utils\MessageStatus::getDecEntryForStatus(Utils\MessageStatus::FILTER_ALL))
            ->setListTo(new \DateTimeImmutable())
            ->setListFrom((new \DateTimeImmutable())->modify('-1 day'));

        $listrecRes = $client->getListOfSentMessages($account, $listrec);
        self::assertTrue($listrecRes->getStatus()->isOk(), $listrecRes->getStatus()->getMessage());

        $client = $this->createConnector();

        $request = new DTO\Request\GetSignedDeliveryInfo();
        $request->setDataMessageId($listrecRes->getRecord()[0]->getDataMessageId());

        $response = $client->getSignedDeliveryInfo($account, $request);

        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());
    }

    public function testMessageStateChanges(): void
    {
        $account = $this->createOVMAccount();
        $client = $this->createConnector();

        $request = new DTO\Request\GetMessageStateChanges();
        $request
            ->setChangesTo(new \DateTimeImmutable())
            ->setChangesFrom((new \DateTimeImmutable())->modify('-1 day'));

        $response = $client->getMessageStateChanges($account, $request);

        self::assertTrue($response->getStatus()->isOk(), $response->getStatus()->getMessage());
    }

    private function getNewMessageEnvelope(): DTO\Request\CreateMessage
    {
        $createMessage = new DTO\Request\CreateMessage();
        $createMessage
            ->setEnvelope((new DTO\Envelope()));
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

        $file = new DTO\File();
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
