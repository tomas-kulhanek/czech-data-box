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
use TomasKulhanek\CzechDataBox\Exception\FileSizeOverflow;
use TomasKulhanek\CzechDataBox\Exception\MissingMainFile;
use TomasKulhanek\CzechDataBox\Exception\MissingRequiredField;
use TomasKulhanek\CzechDataBox\Exception\RecipientCountOverflow;
use TomasKulhanek\CzechDataBox\Provider\ClientProviderInterface;
use TomasKulhanek\CzechDataBox\Utils\AllowedAttachmentFormats;
use TomasKulhanek\CzechDataBox\Utils\BinarySuffix;

readonly class Connector
{
    /**
     * Jedna hromadná zpráva může mít nejvýše 50 adresátů
     * (Provozní řád ISDS, část II, kap. 5, Hromadná datová zpráva).
     */
    public const int MAX_RECIPIENT_COUNT = 50;

    /**
     * Maximální počet příloh v jedné datové zprávě
     * (Provozní řád ISDS, část II, kap. 5, Omezení velikosti datové zprávy).
     */
    public const int MAX_ATTACHMENT_COUNT = 100;

    /**
     * Kontejnerových příloh (ZIP/ASiC) smí být v jedné zprávě nejvýše 10.
     */
    public const int MAX_CONTAINER_COUNT = 10;

    /**
     * Maximální souhrnná velikost příloh běžné datové zprávy.
     */
    public const int MAX_MESSAGE_SIZE = 20 * 1024 ** 2;

    /**
     * Maximální souhrnná velikost příloh velkoobjemové datové zprávy (VoDZ).
     */
    public const int MAX_BIG_MESSAGE_SIZE = 100 * 1024 ** 2;

    /**
     * Horní mez velikosti SOAP odpovědi, kterou je knihovna ochotná parsovat.
     * VoDZ o 100 MB narůstá Base64 kódováním zhruba o třetinu, k tomu se
     * připočítává obálka, proto je výchozí limit nastavený s rezervou.
     */
    public const int DEFAULT_MAX_RESPONSE_SIZE = 256 * 1024 ** 2;

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
        $sumFileSize = $this->assertAttachmentFormats($input->getFiles());
        $this->assertAttachmentSize($sumFileSize, self::MAX_MESSAGE_SIZE);
        if (!$input->getMainFile() instanceof File) {
            throw new MissingMainFile('The message can\'t be send without main attachment');
        }
        if (empty($input->getEnvelope()->getAnnotation())) {
            throw new MissingRequiredField('annotation');
        }
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
        $this->assertAttachmentSize((int) $content->getSize(), self::MAX_BIG_MESSAGE_SIZE);

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
        $this->assertAttachmentSize(
            $this->assertAttachmentFormats($inlineFiles),
            self::MAX_BIG_MESSAGE_SIZE
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

    /**
     * @throws AttachmentCountOverflow
     */
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
     * Ověří formát a počet kontejnerových příloh a vrátí souhrnnou velikost jejich obsahu v bajtech.
     *
     * @param File[] $files
     * @throws AttachmentCountOverflow
     * @throws DisallowedAttachmentFormat
     */
    private function assertAttachmentFormats(array $files): int
    {
        $sumFileSize = 0;
        $containerCount = 0;
        foreach ($files as $file) {
            if (!AllowedAttachmentFormats::isAllowed($file->getDescription())) {
                throw new DisallowedAttachmentFormat(
                    sprintf('The attachment "%s" has a format disallowed by the ISDS decree.', $file->getDescription())
                );
            }
            if (AllowedAttachmentFormats::isContainer($file->getDescription())) {
                $containerCount++;
            }
            if ($file->getEncodedContent() instanceof SplFileInfo) {
                $sumFileSize += $file->getEncodedContent()->getSize();
            } elseif ($file->getXmlContent() !== null) {
                $sumFileSize += strlen($file->getXmlContent());
            }
        }
        if ($containerCount > self::MAX_CONTAINER_COUNT) {
            throw new AttachmentCountOverflow(
                sprintf(
                    'A message can contain at most %d container (ZIP/ASiC) attachments. Currently, %d are added.',
                    self::MAX_CONTAINER_COUNT,
                    $containerCount
                )
            );
        }
        return $sumFileSize;
    }

    /**
     * @throws FileSizeOverflow
     */
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

    /**
     * @throws ConnectionException
     */
    private function getXmlDocument(?string $xmlContent = null, bool $soap12 = false): DOMDocument
    {
        if ($xmlContent !== null) {
            return $this->loadXmlDocument($xmlContent);
        }
        $soapNamespace = $soap12
            ? 'http://www.w3.org/2003/05/soap-envelope'
            : 'http://schemas.xmlsoap.org/soap/envelope/';

        return $this->loadXmlDocument(
            '<SOAP-ENV:Envelope xmlns:SOAP-ENV="' . $soapNamespace . '"><SOAP-ENV:Header/><SOAP-ENV:Body></SOAP-ENV:Body></SOAP-ENV:Envelope>'
        );
    }

    /**
     * Načte XML s vypnutým reportováním libxml chyb, aby se poškozená odpověď ISDS
     * projevila jako ConnectionException a ne jako PHP warning v outputu.
     *
     * @throws ConnectionException
     */
    private function loadXmlDocument(string $xmlContent): DOMDocument
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $previousState = libxml_use_internal_errors(true);
        libxml_clear_errors();
        try {
            if (!$document->loadXML($xmlContent)) {
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

    private function getValueByXpath(DOMDocument $document, string $xpath): ?string
    {
        $domXpath = new DOMXPath($document);
        $res = $domXpath->evaluate($xpath);
        if (!$res instanceof DOMNodeList) {
            return null;
        }
        $result = null;
        foreach ($res as $node) {
            if ($node instanceof DOMElement || $node instanceof DOMDocument) {
                $nodeValue = null;
                $children = $node->childNodes;
                foreach ($children as $child) {
                    $nodeValue .= $document->saveXML($child);
                }
            } else {
                $nodeValue = $node->nodeValue;
            }
            $result .= $nodeValue;
        }
        return $result;
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

        $request = $this->serializer->serialize($request, 'xml');
        $request = $this->getXmlDocument($request);

        $requestDocument = $this->getXmlDocument(null, $serviceType->usesSoap12());
        $requestDocumentXpath = new DOMXPath($requestDocument);
        if (empty($requestDocument->documentElement)) {
            throw new ConnectionException();
        }
        $bodyNode = $requestDocumentXpath->evaluate('//' . $requestDocument->documentElement->prefix . ':Body');
        if (!$bodyNode instanceof DOMNodeList) {
            throw new ConnectionException();
        }
        $body = $bodyNode->item(0);
        if (!$body instanceof DOMNode || $body->ownerDocument === null || $request->documentElement === null) {
            throw new ConnectionException();
        }
        $new = $body->ownerDocument->importNode($request->documentElement, true);
        if ($body->nextSibling !== null) {
            $body->insertBefore($new, $body->nextSibling);
        } else {
            $body->appendChild($new);
        }
        $xmlBody = $requestDocument->saveXml();
        if (!$xmlBody) {
            throw new ConnectionException();
        }

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
        if (empty($soapResponse->documentElement)) {
            throw new ConnectionException('The response is empty');
        }
        $response = $this->getValueByXpath($soapResponse, '//' . $soapResponse->documentElement->prefix . ':Body');
        $soapResponse = null;
        $dom = $this->getXmlDocument($response);
        if (empty($dom->documentElement)) {
            throw new ConnectionException('The response is empty');
        }
        $prefix = $dom->documentElement->prefix;
        if ($prefix !== 'p') {
            $dom->documentElement->setAttributeNS(
                'http://www.w3.org/2000/xmlns/',
                'xmlns:p',
                'https://isds.czechpoint.cz/v20'
            );
            /** @var string $response */
            $response = $dom->saveXML();
            $regex = ['/(<|<\/)' . $prefix . ':(\w*)(\s|>|\/>)/'];
            $replace = ['\1p:\2\3'];
            $response = preg_replace($regex, $replace, $response);
        }
        if (empty($response)) {
            throw new ConnectionException('The response is empty');
        }
        $response = str_replace('http://isds.czechpoint.cz/v20', 'https://isds.czechpoint.cz/v20', $response);

        $deserialized = $this->serializer->deserialize($response, $responseClass, 'xml');
        if (!$deserialized instanceof $responseClass) {
            throw new ConnectionException('The response could not be deserialized into ' . $responseClass);
        }

        return $deserialized;
    }
}
