<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox;

use Deprecated;
use SplFileInfo;
use TomasKulhanek\CzechDataBox\DTO\Response\DummyOperation;
use TomasKulhanek\CzechDataBox\DTO\Response\GetOwnerInfoFromLogin2;
use TomasKulhanek\CzechDataBox\DTO\Response\GetUserInfoFromLogin2;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use JMS\Serializer\SerializerInterface;
use TomasKulhanek\CzechDataBox\DTO\ExtFile;
use TomasKulhanek\CzechDataBox\DTO\BigMessageEnvelope;
use TomasKulhanek\CzechDataBox\DTO\Envelope;
use TomasKulhanek\CzechDataBox\DTO\File;
use TomasKulhanek\CzechDataBox\DTO\Request\ArchiveISDSDocument;
use TomasKulhanek\CzechDataBox\DTO\Request\AuthenticateBigMessage;
use TomasKulhanek\CzechDataBox\DTO\Request\AuthenticateMessage;
use TomasKulhanek\CzechDataBox\DTO\Request\BigMessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Request\ChangeISDSPassword;
use TomasKulhanek\CzechDataBox\DTO\Request\CheckDataBox;
use TomasKulhanek\CzechDataBox\DTO\Request\CreateBigMessage;
use TomasKulhanek\CzechDataBox\DTO\Request\CreateMessage;
use TomasKulhanek\CzechDataBox\DTO\Request\DownloadAttachment;
use TomasKulhanek\CzechDataBox\DTO\Request\DataBoxCreditInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\DTInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\EraseMessage;
use TomasKulhanek\CzechDataBox\DTO\Request\FindDataBox2;
use TomasKulhanek\CzechDataBox\DTO\Request\AddDataBoxUser2;
use TomasKulhanek\CzechDataBox\DTO\Request\DeleteDataBoxUser2;
use TomasKulhanek\CzechDataBox\DTO\Request\GetConstants;
use TomasKulhanek\CzechDataBox\DTO\Request\GetDataBoxUsers2;
use TomasKulhanek\CzechDataBox\DTO\Request\UpdateDataBoxUser2;
use TomasKulhanek\CzechDataBox\DTO\Request\GetDataBoxActivityStatus;
use TomasKulhanek\CzechDataBox\DTO\Request\GetDataBoxAddress;
use TomasKulhanek\CzechDataBox\DTO\Request\GetDataBoxList;
use TomasKulhanek\CzechDataBox\DTO\Request\GetDeliveryInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\GetListForNotifications;
use TomasKulhanek\CzechDataBox\DTO\Request\GetListOfErasedMessages;
use TomasKulhanek\CzechDataBox\DTO\Request\GetListOfReceivedMessages;
use TomasKulhanek\CzechDataBox\DTO\Request\GetListOfSentMessages;
use TomasKulhanek\CzechDataBox\DTO\Request\GetMessageAuthor2;
use TomasKulhanek\CzechDataBox\DTO\Request\GetMessageStateChanges;
use TomasKulhanek\CzechDataBox\DTO\Request\GetSignedDeliveryInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\Request;
use TomasKulhanek\CzechDataBox\DTO\Request\ISDSSearch3;
use TomasKulhanek\CzechDataBox\DTO\Request\MarkMessageAsDownloaded;
use TomasKulhanek\CzechDataBox\DTO\Request\MessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Request\MessageEnvelopeDownload;
use TomasKulhanek\CzechDataBox\DTO\Request\PDZInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\PDZSendInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\PickUpAsyncResponse;
use TomasKulhanek\CzechDataBox\DTO\Request\RegisterForNotifications;
use TomasKulhanek\CzechDataBox\DTO\Request\ResignISDSDocument;
use TomasKulhanek\CzechDataBox\DTO\Request\SentMessageEnvelopeDownload;
use TomasKulhanek\CzechDataBox\DTO\Request\SignedBigMessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Request\SignedMessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Request\SignedSentBigMessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Request\SignedSentMessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Request\SuspMessageReport;
use TomasKulhanek\CzechDataBox\DTO\Request\UploadAttachment;
use TomasKulhanek\CzechDataBox\DTO\Request\VerifyMessage;
use TomasKulhanek\CzechDataBox\DTO\Response\GetOwnerInfoFromLogin;
use TomasKulhanek\CzechDataBox\DTO\Response\GetPasswordInfo;
use TomasKulhanek\CzechDataBox\DTO\Response\GetUserInfoFromLogin;
use TomasKulhanek\CzechDataBox\DTO\Response\Response;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;
use TomasKulhanek\CzechDataBox\Exception\AttachmentCountOverflow;
use TomasKulhanek\CzechDataBox\Exception\ConnectionException;
use TomasKulhanek\CzechDataBox\Exception\DisallowedAttachmentFormat;
use TomasKulhanek\CzechDataBox\Exception\FieldLengthOverflow;
use TomasKulhanek\CzechDataBox\Exception\FileSizeOverflow;
use TomasKulhanek\CzechDataBox\Exception\MissingMainFile;
use TomasKulhanek\CzechDataBox\Exception\MissingRequiredField;
use TomasKulhanek\CzechDataBox\Exception\RecipientCountOverflow;
use TomasKulhanek\CzechDataBox\Exception\SoapFault;
use TomasKulhanek\CzechDataBox\Provider\ClientProviderInterface;
use TomasKulhanek\CzechDataBox\Utils\AllowedAttachmentFormats;
use TomasKulhanek\CzechDataBox\Utils\BinarySuffix;

readonly class Connector
{
    public const int MAX_RECIPIENT_COUNT = 50;

    public const int MAX_ATTACHMENT_COUNT = 100;

    public const int MAX_CONTAINER_ATTACHMENT_COUNT = 10;

    public const int MAX_MESSAGE_ATTACHMENTS_SIZE = 20 * 1024 ** 2;

    public const int MAX_BIG_MESSAGE_ATTACHMENTS_SIZE = 100 * 1024 ** 2;

    public const int MAX_ANNOTATION_LENGTH = 255;

    public const int MAX_REF_NUMBER_LENGTH = 50;

    public const int MAX_IDENT_LENGTH = 50;

    public const int DEFAULT_MAX_RESPONSE_SIZE = 256 * 1024 ** 2;

    private const array SOAP_NAMESPACES = [
        'soap11' => 'http://schemas.xmlsoap.org/soap/envelope/',
        'soap12' => 'http://www.w3.org/2003/05/soap-envelope',
    ];

    public function __construct(
        private SerializerInterface $serializer,
        private ClientProviderInterface $provider,
        private int $maxResponseSize = self::DEFAULT_MAX_RESPONSE_SIZE
    ) {
    }

    public function findDataBox2(Account $account, FindDataBox2 $input): DTO\Response\FindDataBox2
    {
        return $this->send($account, ServiceTypeEnum::SEARCH, $input, DTO\Response\FindDataBox2::class);
    }

    public function pdzInfo(Account $account, PDZInfo $input): DTO\Response\PDZInfo
    {
        return $this->send($account, ServiceTypeEnum::SEARCH, $input, DTO\Response\PDZInfo::class);
    }

    public function dataBoxCreditInfo(Account $account, DataBoxCreditInfo $input): DTO\Response\DataBoxCreditInfo
    {
        return $this->send($account, ServiceTypeEnum::SEARCH, $input, DTO\Response\DataBoxCreditInfo::class);
    }

    public function isdsSearch3(Account $account, ISDSSearch3 $input): DTO\Response\ISDSSearch3
    {
        return $this->send($account, ServiceTypeEnum::SEARCH, $input, DTO\Response\ISDSSearch3::class);
    }

    public function getDataBoxActivityStatus(
        Account $account,
        GetDataBoxActivityStatus $input
    ): DTO\Response\GetDataBoxActivityStatus {
        return $this->send($account, ServiceTypeEnum::SEARCH, $input, DTO\Response\GetDataBoxActivityStatus::class);
    }

    public function dtInfo(Account $account, DTInfo $input): DTO\Response\DTInfo
    {
        return $this->send($account, ServiceTypeEnum::SEARCH, $input, DTO\Response\DTInfo::class);
    }

    public function pdzSendInfo(Account $account, PDZSendInfo $input): DTO\Response\PDZSendInfo
    {
        return $this->send($account, ServiceTypeEnum::SEARCH, $input, DTO\Response\PDZSendInfo::class);
    }

    public function getDataBoxList(Account $account, GetDataBoxList $input): DTO\Response\GetDataBoxList
    {
        return $this->send($account, ServiceTypeEnum::SEARCH, $input, DTO\Response\GetDataBoxList::class);
    }

    public function checkDataBox(Account $account, CheckDataBox $input): DTO\Response\CheckDataBox
    {
        return $this->send($account, ServiceTypeEnum::SEARCH, $input, DTO\Response\CheckDataBox::class);
    }

    #[Deprecated(message: 'Use getOwnerInfoFromLogin2() which returns extended data (aifoIsds, dbIdOVM, RUIAN address).')]
    public function getOwnerInfoFromLogin(Account $account): GetOwnerInfoFromLogin
    {
        return $this->send(
            $account,
            ServiceTypeEnum::ACCESS,
            (new DTO\Request\GetOwnerInfoFromLogin()),
            GetOwnerInfoFromLogin::class
        );
    }

    public function getDataBoxUsers2(Account $account, GetDataBoxUsers2 $input): DTO\Response\GetDataBoxUsers2
    {
        return $this->send($account, ServiceTypeEnum::ACCESS, $input, DTO\Response\GetDataBoxUsers2::class);
    }

    public function addDataBoxUser2(Account $account, AddDataBoxUser2 $input): DTO\Response\AddDataBoxUser2
    {
        return $this->send($account, ServiceTypeEnum::ACCESS, $input, DTO\Response\AddDataBoxUser2::class);
    }

    public function updateDataBoxUser2(Account $account, UpdateDataBoxUser2 $input): DTO\Response\UpdateDataBoxUser2
    {
        return $this->send($account, ServiceTypeEnum::ACCESS, $input, DTO\Response\UpdateDataBoxUser2::class);
    }

    public function deleteDataBoxUser2(Account $account, DeleteDataBoxUser2 $input): DTO\Response\DeleteDataBoxUser2
    {
        return $this->send($account, ServiceTypeEnum::ACCESS, $input, DTO\Response\DeleteDataBoxUser2::class);
    }

    public function changeIsdsPassword(Account $account, ChangeISDSPassword $input): DTO\Response\ChangeISDSPassword
    {
        return $this->send($account, ServiceTypeEnum::ACCESS, $input, DTO\Response\ChangeISDSPassword::class);
    }

    public function getPasswordExpirationInfo(Account $account): GetPasswordInfo
    {
        return $this->send(
            $account,
            ServiceTypeEnum::ACCESS,
            (new DTO\Request\GetPasswordInfo()),
            GetPasswordInfo::class
        );
    }

    public function authenticateMessage(Account $account, AuthenticateMessage $input): DTO\Response\AuthenticateMessage
    {
        return $this->send($account, ServiceTypeEnum::OPERATIONS, $input, DTO\Response\AuthenticateMessage::class);
    }

    #[Deprecated(message: 'Use getUserInfoFromLogin2() which returns extended data (aifoIsds, isdsID, RUIAN address).')]
    public function getUserInfoFromLogin(Account $account): GetUserInfoFromLogin
    {
        return $this->send(
            $account,
            ServiceTypeEnum::ACCESS,
            (new DTO\Request\GetUserInfoFromLogin()),
            GetUserInfoFromLogin::class
        );
    }

    #[Deprecated]
    public function verifyMessage(Account $account, VerifyMessage $input): DTO\Response\VerifyMessage
    {
        return $this->send($account, ServiceTypeEnum::INFO, $input, DTO\Response\VerifyMessage::class);
    }

    public function createMessage(Account $account, CreateMessage $input): DTO\Response\CreateMessage
    {
        $recipientsCount = count($input->getRecipients());
        if ($recipientsCount < 1) {
            throw new MissingRequiredField('recipient');
        }
        if ($recipientsCount > self::MAX_RECIPIENT_COUNT) {
            throw new RecipientCountOverflow(
                sprintf(
                    'More than %d recipients are assigned. Currently, %d are added.',
                    self::MAX_RECIPIENT_COUNT,
                    $recipientsCount
                )
            );
        }
        $this->assertAttachmentCount(count($input->getFiles()));
        $this->assertAllowedFormats($input->getFiles());
        $this->assertContainerCount($input->getFiles());
        $this->assertAttachmentSize(
            $this->sumAttachmentSize($input->getFiles()),
            self::MAX_MESSAGE_ATTACHMENTS_SIZE
        );
        if (!$input->getMainFile() instanceof File) {
            throw new MissingMainFile('The message can\'t be send without main attachment');
        }
        if (empty($input->getEnvelope()->getAnnotation())) {
            throw new MissingRequiredField('annotation');
        }
        $this->assertEnvelopeLengths($input->getEnvelope());
        return $this->send($account, ServiceTypeEnum::OPERATIONS, $input, DTO\Response\CreateMessage::class);
    }

    public function messageDownload(Account $account, MessageDownload $input): DTO\Response\MessageDownload
    {
        return $this->send($account, ServiceTypeEnum::OPERATIONS, $input, DTO\Response\MessageDownload::class);
    }

    public function signedMessageDownload(
        Account $account,
        SignedMessageDownload $input
    ): DTO\Response\SignedMessageDownload {
        return $this->send($account, ServiceTypeEnum::OPERATIONS, $input, DTO\Response\SignedMessageDownload::class);
    }

    public function signedSentMessageDownload(
        Account $account,
        SignedSentMessageDownload $input
    ): DTO\Response\SignedSentMessageDownload {
        return $this->send(
            $account,
            ServiceTypeEnum::OPERATIONS,
            $input,
            DTO\Response\SignedSentMessageDownload::class
        );
    }

    public function resignIsdsDocument(Account $account, ResignISDSDocument $input): DTO\Response\ResignISDSDocument
    {
        return $this->send($account, ServiceTypeEnum::OPERATIONS, $input, DTO\Response\ResignISDSDocument::class);
    }

    public function messageEnvelopeDownload(
        Account $account,
        MessageEnvelopeDownload $input
    ): DTO\Response\MessageEnvelopeDownload {
        return $this->send($account, ServiceTypeEnum::INFO, $input, DTO\Response\MessageEnvelopeDownload::class);
    }

    public function markMessageAsDownloaded(
        Account $account,
        MarkMessageAsDownloaded $input
    ): DTO\Response\MarkMessageAsDownloaded {
        return $this->send($account, ServiceTypeEnum::INFO, $input, DTO\Response\MarkMessageAsDownloaded::class);
    }

    public function getDeliveryInfo(Account $account, GetDeliveryInfo $input): DTO\Response\GetDeliveryInfo
    {
        return $this->send($account, ServiceTypeEnum::INFO, $input, DTO\Response\GetDeliveryInfo::class);
    }

    public function getSignedDeliveryInfo(
        Account $account,
        GetSignedDeliveryInfo $input
    ): DTO\Response\GetSignedDeliveryInfo {
        return $this->send($account, ServiceTypeEnum::INFO, $input, DTO\Response\GetSignedDeliveryInfo::class);
    }

    public function getListOfSentMessages(
        Account $account,
        GetListOfSentMessages $input
    ): DTO\Response\GetListOfSentMessages {
        return $this->send($account, ServiceTypeEnum::INFO, $input, DTO\Response\GetListOfSentMessages::class);
    }

    public function getListOfReceivedMessages(
        Account $account,
        GetListOfReceivedMessages $input
    ): DTO\Response\GetListOfReceivedMessages {
        return $this->send($account, ServiceTypeEnum::INFO, $input, DTO\Response\GetListOfReceivedMessages::class);
    }

    public function getMessageStateChanges(
        Account $account,
        GetMessageStateChanges $input
    ): DTO\Response\GetMessageStateChanges {
        return $this->send($account, ServiceTypeEnum::INFO, $input, DTO\Response\GetMessageStateChanges::class);
    }

    public function sentMessageEnvelopeDownload(
        Account $account,
        SentMessageEnvelopeDownload $input
    ): DTO\Response\SentMessageEnvelopeDownload {
        return $this->send($account, ServiceTypeEnum::INFO, $input, DTO\Response\SentMessageEnvelopeDownload::class);
    }

    public function getMessageAuthor2(Account $account, GetMessageAuthor2 $input): DTO\Response\GetMessageAuthor2
    {
        return $this->send($account, ServiceTypeEnum::INFO, $input, DTO\Response\GetMessageAuthor2::class);
    }

    public function eraseMessage(Account $account, EraseMessage $input): DTO\Response\EraseMessage
    {
        return $this->send($account, ServiceTypeEnum::INFO, $input, DTO\Response\EraseMessage::class);
    }

    public function getListOfErasedMessages(
        Account $account,
        GetListOfErasedMessages $input
    ): DTO\Response\GetListOfErasedMessages {
        return $this->send($account, ServiceTypeEnum::INFO, $input, DTO\Response\GetListOfErasedMessages::class);
    }

    public function pickUpAsyncResponse(
        Account $account,
        PickUpAsyncResponse $input
    ): DTO\Response\PickUpAsyncResponse {
        return $this->send($account, ServiceTypeEnum::INFO, $input, DTO\Response\PickUpAsyncResponse::class);
    }

    public function getListForNotifications(
        Account $account,
        GetListForNotifications $input
    ): DTO\Response\GetListForNotifications {
        return $this->send($account, ServiceTypeEnum::INFO, $input, DTO\Response\GetListForNotifications::class);
    }

    public function registerForNotifications(
        Account $account,
        RegisterForNotifications $input
    ): DTO\Response\RegisterForNotifications {
        return $this->send($account, ServiceTypeEnum::INFO, $input, DTO\Response\RegisterForNotifications::class);
    }

    public function suspMessageReport(Account $account, SuspMessageReport $input): DTO\Response\SuspMessageReport
    {
        return $this->send($account, ServiceTypeEnum::INFO, $input, DTO\Response\SuspMessageReport::class);
    }

    public function dummyOperation(Account $account): DummyOperation
    {
        return $this->send(
            $account,
            ServiceTypeEnum::OPERATIONS,
            (new DTO\Request\DummyOperation()),
            DummyOperation::class
        );
    }

    public function getConstants(Account $account, GetConstants $input): DTO\Response\GetConstants
    {
        return $this->send($account, ServiceTypeEnum::SEARCH, $input, DTO\Response\GetConstants::class);
    }

    public function getDataBoxAddress(Account $account, GetDataBoxAddress $input): DTO\Response\GetDataBoxAddress
    {
        return $this->send($account, ServiceTypeEnum::SEARCH, $input, DTO\Response\GetDataBoxAddress::class);
    }

    public function getOwnerInfoFromLogin2(Account $account): GetOwnerInfoFromLogin2
    {
        return $this->send(
            $account,
            ServiceTypeEnum::ACCESS,
            (new DTO\Request\GetOwnerInfoFromLogin2()),
            GetOwnerInfoFromLogin2::class
        );
    }

    public function getUserInfoFromLogin2(Account $account): GetUserInfoFromLogin2
    {
        return $this->send(
            $account,
            ServiceTypeEnum::ACCESS,
            (new DTO\Request\GetUserInfoFromLogin2()),
            GetUserInfoFromLogin2::class
        );
    }

    public function uploadAttachment(Account $account, UploadAttachment $input): DTO\Response\UploadAttachment
    {
        $file = $input->getFile();
        if (trim($file->getDescription()) === '') {
            throw new MissingRequiredField('dmFileDescr');
        }
        if (!AllowedAttachmentFormats::isAllowed($file->getDescription())) {
            throw new DisallowedAttachmentFormat(
                sprintf('The attachment "%s" has a format disallowed by the ISDS decree.', $file->getDescription())
            );
        }
        $content = $file->getEncodedContent();
        if (!$content instanceof SplFileInfo) {
            throw new MissingRequiredField('dmEncodedContent');
        }
        $this->assertAttachmentSize((int) $content->getSize(), self::MAX_BIG_MESSAGE_ATTACHMENTS_SIZE);

        return $this->send($account, ServiceTypeEnum::VODZ, $input, DTO\Response\UploadAttachment::class);
    }

    public function downloadAttachment(Account $account, DownloadAttachment $input): DTO\Response\DownloadAttachment
    {
        return $this->send($account, ServiceTypeEnum::VODZ, $input, DTO\Response\DownloadAttachment::class);
    }

    public function createBigMessage(Account $account, CreateBigMessage $input): DTO\Response\CreateBigMessage
    {
        $extFiles = $input->getFiles()->getExtFiles();
        $inlineFiles = $input->getFiles()->getFiles();
        if ($extFiles === []) {
            throw new MissingRequiredField('dmExtFile');
        }
        $this->assertAttachmentCount(count($extFiles) + count($inlineFiles));
        $this->assertAllowedFormats($inlineFiles);
        $this->assertContainerCount($inlineFiles);
        $this->assertAttachmentSize(
            $this->sumAttachmentSize($inlineFiles),
            self::MAX_BIG_MESSAGE_ATTACHMENTS_SIZE
        );
        if (!$this->hasMainAttachment($extFiles, $inlineFiles)) {
            throw new MissingMainFile('The message can\'t be send without main attachment');
        }
        $envelope = $input->getEnvelope();
        if (empty($envelope->getRecipientId())) {
            throw new MissingRequiredField('dbIDRecipient');
        }
        if (empty($envelope->getAnnotation())) {
            throw new MissingRequiredField('annotation');
        }
        $this->assertEnvelopeLengths($envelope);

        return $this->send($account, ServiceTypeEnum::VODZ, $input, DTO\Response\CreateBigMessage::class);
    }

    public function authenticateBigMessage(
        Account $account,
        AuthenticateBigMessage $input
    ): DTO\Response\AuthenticateBigMessage {
        return $this->send($account, ServiceTypeEnum::VODZ, $input, DTO\Response\AuthenticateBigMessage::class);
    }

    public function bigMessageDownload(Account $account, BigMessageDownload $input): DTO\Response\BigMessageDownload
    {
        return $this->send($account, ServiceTypeEnum::VODZ, $input, DTO\Response\BigMessageDownload::class);
    }

    public function signedBigMessageDownload(
        Account $account,
        SignedBigMessageDownload $input
    ): DTO\Response\SignedBigMessageDownload {
        return $this->send($account, ServiceTypeEnum::VODZ, $input, DTO\Response\SignedBigMessageDownload::class);
    }

    public function signedSentBigMessageDownload(
        Account $account,
        SignedSentBigMessageDownload $input
    ): DTO\Response\SignedSentBigMessageDownload {
        return $this->send($account, ServiceTypeEnum::VODZ, $input, DTO\Response\SignedSentBigMessageDownload::class);
    }

    public function archiveIsdsDocument(
        Account $account,
        ArchiveISDSDocument $input
    ): DTO\Response\ArchiveISDSDocument {
        return $this->send($account, ServiceTypeEnum::ARCHIVE, $input, DTO\Response\ArchiveISDSDocument::class);
    }

    private function assertAttachmentCount(int $count): void
    {
        if ($count > self::MAX_ATTACHMENT_COUNT) {
            throw new AttachmentCountOverflow(
                sprintf(
                    'A message can contain at most %d attachments. Currently, %d are added.',
                    self::MAX_ATTACHMENT_COUNT,
                    $count
                )
            );
        }
    }

    /**
     * @param File[] $files
     */
    private function assertAllowedFormats(array $files): void
    {
        foreach ($files as $file) {
            if (!AllowedAttachmentFormats::isAllowed($file->getDescription())) {
                throw new DisallowedAttachmentFormat(
                    sprintf('The attachment "%s" has a format disallowed by the ISDS decree.', $file->getDescription())
                );
            }
        }
    }

    /**
     * @param File[] $files
     */
    private function assertContainerCount(array $files): void
    {
        $containerCount = count(
            array_filter($files, static fn (File $file): bool => AllowedAttachmentFormats::isContainer(
                $file->getDescription()
            ))
        );
        if ($containerCount > self::MAX_CONTAINER_ATTACHMENT_COUNT) {
            throw new AttachmentCountOverflow(
                sprintf(
                    'A message can contain at most %d container (ZIP/ASiC) attachments. Currently, %d are added.',
                    self::MAX_CONTAINER_ATTACHMENT_COUNT,
                    $containerCount
                )
            );
        }
    }

    /**
     * @param File[] $files
     */
    private function sumAttachmentSize(array $files): int
    {
        $sumFileSize = 0;
        foreach ($files as $file) {
            if ($file->getEncodedContent() instanceof SplFileInfo) {
                $sumFileSize += $file->getEncodedContent()->getSize();
            } elseif ($file->getXmlContent() !== null) {
                $sumFileSize += strlen($file->getXmlContent());
            }
        }

        return $sumFileSize;
    }

    private function assertEnvelopeLengths(Envelope|BigMessageEnvelope $envelope): void
    {
        $this->assertFieldLength('dmAnnotation', $envelope->getAnnotation(), self::MAX_ANNOTATION_LENGTH);
        $this->assertFieldLength(
            'dmRecipientRefNumber',
            $envelope->getRecipientRefNumber(),
            self::MAX_REF_NUMBER_LENGTH
        );
        $this->assertFieldLength('dmSenderRefNumber', $envelope->getSenderRefNumber(), self::MAX_REF_NUMBER_LENGTH);
        $this->assertFieldLength('dmRecipientIdent', $envelope->getRecipientIdent(), self::MAX_IDENT_LENGTH);
        $this->assertFieldLength('dmSenderIdent', $envelope->getSenderIdent(), self::MAX_IDENT_LENGTH);
    }

    private function assertFieldLength(string $fieldName, ?string $value, int $maxLength): void
    {
        if ($value === null) {
            return;
        }
        $length = mb_strlen($value);
        if ($length > $maxLength) {
            throw new FieldLengthOverflow(
                sprintf(
                    'The field \'%s\' can be at most %d characters long. Currently it has %d.',
                    $fieldName,
                    $maxLength,
                    $length
                )
            );
        }
    }

    private function assertAttachmentSize(int $size, int $maxSize): void
    {
        if ($size > $maxSize) {
            throw new FileSizeOverflow(
                sprintf(
                    'Maximum size of all files can be %s. Current size is %s.',
                    BinarySuffix::convert($maxSize),
                    BinarySuffix::convert($size)
                )
            );
        }
    }

    /**
     * @param ExtFile[] $extFiles
     * @param File[] $inlineFiles
     */
    private function hasMainAttachment(array $extFiles, array $inlineFiles): bool
    {
        $isMain = static fn (ExtFile|File $file): bool => $file->getMetaType() === 'main';

        return array_any($extFiles, $isMain) || array_any($inlineFiles, $isMain);
    }

    private function getXmlDocument(?string $xmlContent = null, bool $soap12 = false): DOMDocument
    {
        if ($xmlContent !== null) {
            return $this->loadXmlDocumentOrFail($xmlContent);
        }
        $soapNamespace = $soap12
            ? 'http://www.w3.org/2003/05/soap-envelope'
            : 'http://schemas.xmlsoap.org/soap/envelope/';

        return $this->loadXmlDocumentOrFail(
            '<SOAP-ENV:Envelope xmlns:SOAP-ENV="' . $soapNamespace . '"><SOAP-ENV:Header/><SOAP-ENV:Body></SOAP-ENV:Body></SOAP-ENV:Envelope>'
        );
    }

    private function loadXmlDocumentOrFail(string $xmlContent): DOMDocument
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $previousState = libxml_use_internal_errors(true);
        libxml_clear_errors();
        try {
            if (!$document->loadXML($xmlContent, LIBXML_NONET)) {
                $error = libxml_get_last_error();
                throw new ConnectionException(
                    $error === false
                        ? 'The XML document could not be parsed.'
                        : sprintf('The XML document could not be parsed: %s', trim($error->message))
                );
            }
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousState);
        }

        return $document;
    }

    private function createSoapXpath(DOMDocument $document): DOMXPath
    {
        $xpath = new DOMXPath($document);
        foreach (self::SOAP_NAMESPACES as $prefix => $namespace) {
            $xpath->registerNamespace($prefix, $namespace);
        }

        return $xpath;
    }

    private function getSoapBodyContent(DOMDocument $document): ?string
    {
        $bodies = $this->createSoapXpath($document)->query('//soap11:Body | //soap12:Body');
        if (!$bodies instanceof DOMNodeList) {
            return null;
        }
        $result = null;
        foreach ($bodies as $body) {
            if (!$body instanceof DOMElement) {
                continue;
            }
            foreach ($body->childNodes as $child) {
                $result .= $document->saveXML($child);
            }
        }

        return $result;
    }

    private function throwOnSoapFault(DOMDocument $document): void
    {
        $xpath = $this->createSoapXpath($document);

        $faults = $xpath->query('//soap11:Fault');
        $fault = $faults instanceof DOMNodeList ? $faults->item(0) : null;
        if ($fault instanceof DOMNode) {
            throw new SoapFault(
                $this->evaluateXpathString($xpath, 'string(faultcode)', $fault) ?? 'SOAP-ENV:Server',
                $this->evaluateXpathString($xpath, 'string(faultstring)', $fault) ?? 'Unknown SOAP fault'
            );
        }

        $faults = $xpath->query('//soap12:Fault');
        $fault = $faults instanceof DOMNodeList ? $faults->item(0) : null;
        if ($fault instanceof DOMNode) {
            throw new SoapFault(
                $this->evaluateXpathString($xpath, 'string(soap12:Code/soap12:Value)', $fault) ?? 'env:Receiver',
                $this->evaluateXpathString($xpath, 'string(soap12:Reason/soap12:Text)', $fault) ?? 'Unknown SOAP fault'
            );
        }
    }

    private function evaluateXpathString(DOMXPath $xpath, string $expression, DOMNode $context): ?string
    {
        $value = $xpath->evaluate($expression, $context);

        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    /**
     * @template T of DTO\Response\Response
     * @return Response
     * @phpstan-param class-string<T> $responseClass
     * @phpstan-return T
     * @throws Exception\ConnectionException
     */
    protected function send(
        Account $account,
        ServiceTypeEnum $serviceType,
        Request $request,
        string $responseClass
    ): Response {
        if (!is_subclass_of($responseClass, Response::class)) {
            throw new ConnectionException();
        }

        $body = $this->serializer->serialize($request, 'xml');
        if (str_starts_with($body, '<?')) {
            $declarationEnd = strpos($body, '?>');
            if ($declarationEnd !== false) {
                $offset = $declarationEnd + 2;
                $offset += strspn($body, " \t\r\n", $offset);
                $body = substr($body, $offset);
            }
        }
        $soapNamespace = $serviceType->usesSoap12()
            ? 'http://www.w3.org/2003/05/soap-envelope'
            : 'http://schemas.xmlsoap.org/soap/envelope/';
        $xmlBody = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="' . $soapNamespace . '"><SOAP-ENV:Header/><SOAP-ENV:Body>'
            . $body
            . '</SOAP-ENV:Body></SOAP-ENV:Envelope>';
        unset($body);

        $response = $this->provider->sendRequest($account, $serviceType, $xmlBody);
        if (strlen($response) > $this->maxResponseSize) {
            throw new ConnectionException(
                sprintf(
                    'The response is larger than the allowed %s.',
                    BinarySuffix::convert($this->maxResponseSize)
                )
            );
        }
        $soapResponse = $this->getXmlDocument($response);
        $this->throwOnSoapFault($soapResponse);
        if (empty($soapResponse->documentElement)) {
            throw new ConnectionException('The response is empty');
        }
        $response = $this->getSoapBodyContent($soapResponse);
        $soapResponse = null;
        if (empty($response)) {
            throw new ConnectionException('The response is empty');
        }
        $dom = $this->getXmlDocument($response);
        if (empty($dom->documentElement)) {
            throw new ConnectionException('The response is empty');
        }

        $deserialized = $this->serializer->deserialize($response, $responseClass, 'xml');
        if (!$deserialized instanceof $responseClass) {
            throw new ConnectionException('The response could not be deserialized into ' . $responseClass);
        }

        return $deserialized;
    }
}
