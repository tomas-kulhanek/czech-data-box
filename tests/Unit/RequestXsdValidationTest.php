<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use LibXMLError;
use TomasKulhanek\CzechDataBox\DTO\Request\ArchiveISDSDocument;
use TomasKulhanek\CzechDataBox\DTO\Request\AuthenticateBigMessage;
use TomasKulhanek\CzechDataBox\DTO\Request\AuthenticateMessage;
use TomasKulhanek\CzechDataBox\DTO\Request\BigMessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Request\CreateBigMessage;
use TomasKulhanek\CzechDataBox\DTO\Request\CreateMessage;
use TomasKulhanek\CzechDataBox\DTO\Request\DownloadAttachment;
use TomasKulhanek\CzechDataBox\DTO\Request\DummyOperation;
use TomasKulhanek\CzechDataBox\DTO\Request\EraseMessage;
use TomasKulhanek\CzechDataBox\DTO\Request\GetDeliveryInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\GetListForNotifications;
use TomasKulhanek\CzechDataBox\DTO\Request\GetListOfErasedMessages;
use TomasKulhanek\CzechDataBox\DTO\Request\GetListOfReceivedMessages;
use TomasKulhanek\CzechDataBox\DTO\Request\GetListOfSentMessages;
use TomasKulhanek\CzechDataBox\DTO\Request\GetMessageAuthor2;
use TomasKulhanek\CzechDataBox\DTO\Request\GetMessageStateChanges;
use TomasKulhanek\CzechDataBox\DTO\Request\GetSignedDeliveryInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\MarkMessageAsDownloaded;
use TomasKulhanek\CzechDataBox\DTO\Request\MessageDownload;
use TomasKulhanek\CzechDataBox\DTO\Request\MessageEnvelopeDownload;
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
use TomasKulhanek\CzechDataBox\DTO\Request\AddDataBoxUser2;
use TomasKulhanek\CzechDataBox\DTO\Request\ChangeISDSPassword;
use TomasKulhanek\CzechDataBox\DTO\Request\CheckDataBox;
use TomasKulhanek\CzechDataBox\DTO\Request\DTInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\DataBoxCreditInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\DeleteDataBoxUser2;
use TomasKulhanek\CzechDataBox\DTO\Request\FindDataBox2;
use TomasKulhanek\CzechDataBox\DTO\Request\GetConstants;
use TomasKulhanek\CzechDataBox\DTO\Request\GetDataBoxActivityStatus;
use TomasKulhanek\CzechDataBox\DTO\Request\GetDataBoxAddress;
use TomasKulhanek\CzechDataBox\DTO\Request\GetDataBoxList;
use TomasKulhanek\CzechDataBox\DTO\Request\GetDataBoxUsers2;
use TomasKulhanek\CzechDataBox\DTO\Request\GetOwnerInfoFromLogin;
use TomasKulhanek\CzechDataBox\DTO\Request\GetOwnerInfoFromLogin2;
use TomasKulhanek\CzechDataBox\DTO\Request\GetPasswordInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\GetUserInfoFromLogin;
use TomasKulhanek\CzechDataBox\DTO\Request\GetUserInfoFromLogin2;
use TomasKulhanek\CzechDataBox\DTO\Request\ISDSSearch3;
use TomasKulhanek\CzechDataBox\DTO\Request\PDZInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\PDZSendInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\UpdateDataBoxUser2;
use Closure;
use JMS\Serializer\Annotation as Serializer;
use DateTimeImmutable;
use DOMDocument;
use DOMElement;
use DOMXPath;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use TomasKulhanek\CzechDataBox\DTO\BigAttachment;
use TomasKulhanek\CzechDataBox\DTO\BigMessageEnvelope;
use TomasKulhanek\CzechDataBox\DTO\BigMessageFiles;
use TomasKulhanek\CzechDataBox\DTO\Envelope;
use TomasKulhanek\CzechDataBox\DTO\ExtFile;
use TomasKulhanek\CzechDataBox\DTO\File;
use TomasKulhanek\CzechDataBox\DTO\OwnerInfoExt2;
use TomasKulhanek\CzechDataBox\DTO\Recipient;
use TomasKulhanek\CzechDataBox\DTO\Request;
use TomasKulhanek\CzechDataBox\DTO\UserInfoExt2;
use TomasKulhanek\Tests\CzechDataBox\SerializerTrait;
use TomasKulhanek\Serializer\Utils\SplFileInfo;

class RequestXsdValidationTest extends TestCase
{
    use SerializerTrait;

    private const string DM_BASE_TYPES = 'dmBaseTypes.xsd';

    private const string DB_TYPES = 'dbTypes.xsd';

    /**
     * @var list<class-string>
     */
    private const array SKIPPED_REQUESTS = [];

    #[DataProvider('provideRequests')]
    public function testSerializedRequestIsValidAgainstXsd(Closure $factory, string $schemaFile): void
    {
        $xml = self::createSerializer()->serialize($factory(), 'xml');
        $xml = self::normalizeToXsdNamespace($xml);

        $document = new DOMDocument();
        self::assertTrue($document->loadXML($xml));

        libxml_use_internal_errors(true);
        libxml_clear_errors();
        $isValid = $document->schemaValidate(__DIR__ . '/../_data/xsd/' . $schemaFile);
        $errors = array_map(
            static fn (LibXMLError $error): string => trim($error->message),
            libxml_get_errors()
        );
        libxml_clear_errors();
        libxml_use_internal_errors(false);

        self::assertTrue($isValid, sprintf(
            "Serialized request does not validate against %s:\n%s\n\nSerialized XML:\n%s",
            $schemaFile,
            implode("\n", $errors),
            $xml
        ));
    }

    /**
     * CreateMultipleMessage carries tMultipleMessageEnvelopeSub, which - unlike the single
     * message envelope - knows nothing about the recipient. Any element declared on Envelope
     * beyond that type would be rejected by the ISDS schema once it was populated.
     */
    public function testEnvelopeDeclaresOnlyMultipleMessageEnvelopeElements(): void
    {
        $allowed = self::collectXsdElementNames('tMultipleMessageEnvelopeSub');
        self::assertContains('dmSenderOrgUnit', $allowed);
        self::assertNotContains('dmRecipientOrgUnit', $allowed);

        $declared = [];
        foreach (new ReflectionClass(Envelope::class)->getProperties() as $property) {
            if ($property->getAttributes(Serializer\XmlAttribute::class) !== []) {
                continue;
            }
            foreach ($property->getAttributes(Serializer\SerializedName::class) as $attribute) {
                $declared[] = $attribute->newInstance()->name;
            }
        }

        self::assertSame([], array_values(array_diff($declared, $allowed)), sprintf(
            'Envelope declares elements unknown to tMultipleMessageEnvelopeSub: %s',
            implode(', ', array_diff($declared, $allowed))
        ));
    }

    /**
     * Collects the element names a complex type accepts, following xs:group references.
     *
     * @return list<string>
     */
    private static function collectXsdElementNames(string $typeName): array
    {
        $document = new DOMDocument();
        self::assertTrue($document->load(__DIR__ . '/../_data/xsd/' . self::DM_BASE_TYPES));

        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('xs', 'http://www.w3.org/2001/XMLSchema');

        $names = [];
        $queue = [sprintf('/xs:schema/xs:complexType[@name="%s"]', $typeName)];
        while ($queue !== []) {
            $query = array_shift($queue);
            $nodes = $xpath->query($query . '//xs:element[@name]');
            self::assertNotFalse($nodes);
            foreach ($nodes as $node) {
                self::assertInstanceOf(DOMElement::class, $node);
                $names[] = $node->getAttribute('name');
            }

            $groups = $xpath->query($query . '//xs:group[@ref]');
            self::assertNotFalse($groups);
            foreach ($groups as $group) {
                self::assertInstanceOf(DOMElement::class, $group);
                $reference = str_replace('tns:', '', $group->getAttribute('ref'));
                $queue[] = sprintf('/xs:schema/xs:group[@name="%s"]', $reference);
            }
        }

        return array_values(array_unique($names));
    }

    public function testEveryRequestClassIsCovered(): void
    {
        $files = glob(__DIR__ . '/../../src/DTO/Request/*.php');
        self::assertNotFalse($files);
        self::assertNotEmpty($files);

        $concreteClasses = [];
        foreach ($files as $file) {
            /** @var class-string $className */
            $className = 'TomasKulhanek\\CzechDataBox\\DTO\\Request\\' . basename($file, '.php');
            $reflection = new ReflectionClass($className);
            if ($reflection->isAbstract() || $reflection->isInterface()) {
                continue;
            }
            $concreteClasses[] = $className;
        }

        $covered = array_map(
            static fn (string $shortName): string => 'TomasKulhanek\\CzechDataBox\\DTO\\Request\\' . $shortName,
            array_keys(self::provideRequests())
        );
        $missing = array_values(array_diff($concreteClasses, $covered, self::SKIPPED_REQUESTS));

        self::assertSame([], $missing, sprintf(
            'Request classes neither covered by provideRequests() nor skipped: %s',
            implode(', ', $missing)
        ));
    }

    /**
     * @return array<string, array{Closure(): Request\Request, string}>
     */
    public static function provideRequests(): array
    {
        return [
            'ArchiveISDSDocument' => [
                static fn (): ArchiveISDSDocument => new ArchiveISDSDocument()
                    ->setDataMessage(SplFileInfo::createInTemp('obsah')),
                self::DM_BASE_TYPES,
            ],
            'AuthenticateBigMessage' => [
                static fn (): AuthenticateBigMessage => new AuthenticateBigMessage()
                    ->setDataMessage(SplFileInfo::createInTemp('obsah')),
                self::DM_BASE_TYPES,
            ],
            'AuthenticateMessage' => [
                static fn (): AuthenticateMessage => new AuthenticateMessage()
                    ->setDataMessage(SplFileInfo::createInTemp('obsah')),
                self::DM_BASE_TYPES,
            ],
            'BigMessageDownload' => [
                static fn (): BigMessageDownload => new BigMessageDownload()
                    ->setDataMessageId('1234567'),
                self::DM_BASE_TYPES,
            ],
            'CreateBigMessage' => [
                static function (): CreateBigMessage {
                    $envelope = new BigMessageEnvelope();
                    $envelope->setRecipientId('abcdefg');
                    $envelope->setAnnotation('Testovací VoDZ');

                    $extFile = new ExtFile();
                    $extFile->setMetaType('main');
                    $extFile->setAttachmentId('ATT123');
                    $extFile->setAttachmentHash1('aaaa');
                    $extFile->setAttachmentHash1Algorithm('SHA-256');
                    $extFile->setAttachmentHash2('bbbb');
                    $extFile->setAttachmentHash2Algorithm('SHA-512');

                    $files = new BigMessageFiles();
                    $files->addExtFile($extFile);

                    return new CreateBigMessage()
                        ->setEnvelope($envelope)
                        ->setFiles($files);
                },
                self::DM_BASE_TYPES,
            ],
            'CreateMessage' => [
                static function (): CreateMessage {
                    $recipient = new Recipient();
                    $recipient->setDataBoxId('abcdefg');
                    $recipient->setToHand('Jan Novák');

                    $envelope = new Envelope();
                    $envelope->setSenderOrgUnit('Podatelna')
                        ->setSenderOrgUnitNum(1)
                        ->setAnnotation('Testovací zpráva')
                        ->setRecipientRefNumber('cj-1')
                        ->setSenderRefNumber('cj-2')
                        ->setRecipientIdent('sp-1')
                        ->setSenderIdent('sp-2')
                        ->setLegalTitleLaw(300)
                        ->setLegalTitleYear(2008)
                        ->setLegalTitleSect('18')
                        ->setLegalTitlePar('1')
                        ->setLegalTitlePoint('a')
                        ->setPersonalDelivery(false)
                        ->setAllowSubstDelivery(true);

                    $file = new File();
                    $file->setMimeType('application/pdf')
                        ->setMetaType('main')
                        ->setDescription('main.pdf')
                        ->setEncodedContent(SplFileInfo::createInTemp('obsah'));

                    return new CreateMessage()
                        ->setEnvelope($envelope)
                        ->setFiles([$file])
                        ->setRecipients([$recipient]);
                },
                self::DM_BASE_TYPES,
            ],
            'CreateMessageWithFullEnvelope' => [
                self::createFullyPopulatedCreateMessage(...),
                self::DM_BASE_TYPES,
            ],
            'DownloadAttachment' => [
                static fn (): DownloadAttachment => new DownloadAttachment()
                    ->setDataMessageId('1234567')
                    ->setAttachmentNumber(1),
                self::DM_BASE_TYPES,
            ],
            'DummyOperation' => [
                static fn (): DummyOperation => new DummyOperation(),
                self::DM_BASE_TYPES,
            ],
            'EraseMessage' => [
                static fn (): EraseMessage => new EraseMessage()
                    ->setDataMessageId('1234567')
                    ->setIncoming(true),
                self::DM_BASE_TYPES,
            ],
            'GetDeliveryInfo' => [
                static fn (): GetDeliveryInfo => new GetDeliveryInfo()
                    ->setDataMessageId('1234567'),
                self::DM_BASE_TYPES,
            ],
            'GetListForNotifications' => [
                static fn (): GetListForNotifications => new GetListForNotifications()
                    ->setFromTime(new DateTimeImmutable('2026-01-01T10:00:00+01:00'))
                    ->setScope('RECEIVED'),
                self::DM_BASE_TYPES,
            ],
            'GetListOfErasedMessages' => [
                static fn (): GetListOfErasedMessages => new GetListOfErasedMessages()
                    ->setFromDate(new DateTimeImmutable('2026-01-01'))
                    ->setToDate(new DateTimeImmutable('2026-01-31'))
                    ->setMessageType(GetListOfErasedMessages::MESSAGE_TYPE_SENT)
                    ->setOutFormat(GetListOfErasedMessages::OUT_FORMAT_XML),
                self::DM_BASE_TYPES,
            ],
            'GetListOfReceivedMessages' => [
                static fn (): GetListOfReceivedMessages => new GetListOfReceivedMessages()
                    ->setListFrom(new DateTimeImmutable('2026-01-01T00:00:00+01:00'))
                    ->setListTo(new DateTimeImmutable('2026-01-31T23:59:59+01:00'))
                    ->setRecipientOrgUnitNum(1)
                    ->setOffset(1)
                    ->setLimit(10),
                self::DM_BASE_TYPES,
            ],
            'GetListOfSentMessages' => [
                static fn (): GetListOfSentMessages => new GetListOfSentMessages()
                    ->setListFrom(new DateTimeImmutable('2026-01-01T00:00:00+01:00'))
                    ->setListTo(new DateTimeImmutable('2026-01-31T23:59:59+01:00'))
                    ->setSenderOrgUnitNum(1)
                    ->setOffset(1)
                    ->setLimit(10),
                self::DM_BASE_TYPES,
            ],
            'GetMessageAuthor2' => [
                static fn (): GetMessageAuthor2 => new GetMessageAuthor2()
                    ->setDataMessageId('1234567'),
                self::DM_BASE_TYPES,
            ],
            'GetMessageStateChanges' => [
                static fn (): GetMessageStateChanges => new GetMessageStateChanges()
                    ->setChangesFrom(new DateTimeImmutable('2026-01-01T00:00:00+01:00'))
                    ->setChangesTo(new DateTimeImmutable('2026-01-31T23:59:59+01:00')),
                self::DM_BASE_TYPES,
            ],
            'GetSignedDeliveryInfo' => [
                static fn (): GetSignedDeliveryInfo => new GetSignedDeliveryInfo()
                    ->setDataMessageId('1234567'),
                self::DM_BASE_TYPES,
            ],
            'MarkMessageAsDownloaded' => [
                static fn (): MarkMessageAsDownloaded => new MarkMessageAsDownloaded()
                    ->setDataMessageId('1234567'),
                self::DM_BASE_TYPES,
            ],
            'MessageDownload' => [
                static fn (): MessageDownload => new MessageDownload()
                    ->setDataMessageId('1234567'),
                self::DM_BASE_TYPES,
            ],
            'MessageEnvelopeDownload' => [
                static fn (): MessageEnvelopeDownload => new MessageEnvelopeDownload()
                    ->setDataMessageId('1234567'),
                self::DM_BASE_TYPES,
            ],
            'PickUpAsyncResponse' => [
                static fn (): PickUpAsyncResponse => new PickUpAsyncResponse()
                    ->setAsyncId('async-1')
                    ->setAsyncReqType('GetListOfErasedMessages'),
                self::DM_BASE_TYPES,
            ],
            'RegisterForNotifications' => [
                static fn (): RegisterForNotifications => new RegisterForNotifications()
                    ->setAction(1),
                self::DM_BASE_TYPES,
            ],
            'ResignISDSDocument' => [
                static fn (): ResignISDSDocument => new ResignISDSDocument()
                    ->setDocument(SplFileInfo::createInTemp('obsah')),
                self::DM_BASE_TYPES,
            ],
            'SentMessageEnvelopeDownload' => [
                static fn (): SentMessageEnvelopeDownload => new SentMessageEnvelopeDownload()
                    ->setDataMessageId('1234567'),
                self::DM_BASE_TYPES,
            ],
            'SignedBigMessageDownload' => [
                static fn (): SignedBigMessageDownload => new SignedBigMessageDownload()
                    ->setDataMessageId('1234567'),
                self::DM_BASE_TYPES,
            ],
            'SignedMessageDownload' => [
                static fn (): SignedMessageDownload => new SignedMessageDownload()
                    ->setDataMessageId('1234567'),
                self::DM_BASE_TYPES,
            ],
            'SignedSentBigMessageDownload' => [
                static fn (): SignedSentBigMessageDownload => new SignedSentBigMessageDownload()
                    ->setDataMessageId('1234567'),
                self::DM_BASE_TYPES,
            ],
            'SignedSentMessageDownload' => [
                static fn (): SignedSentMessageDownload => new SignedSentMessageDownload()
                    ->setDataMessageId('1234567'),
                self::DM_BASE_TYPES,
            ],
            'SuspMessageReport' => [
                static fn (): SuspMessageReport => new SuspMessageReport()
                    ->setDataMessageId('1234567')
                    ->setReporterName('Jan Novák')
                    ->setReporterMail('jan@example.com')
                    ->setReporterPhone('+420123456789')
                    ->setAllowComplete(false)
                    ->setNote('Podezřelá zpráva'),
                self::DM_BASE_TYPES,
            ],
            'UploadAttachment' => [
                static function (): UploadAttachment {
                    $attachment = new BigAttachment();
                    $attachment->setMimeType('application/pdf')
                        ->setDescription('main.pdf')
                        ->setEncodedContent(SplFileInfo::createInTemp('obsah'));

                    return new UploadAttachment()->setFile($attachment);
                },
                self::DM_BASE_TYPES,
            ],
            'VerifyMessage' => [
                static fn (): VerifyMessage => new VerifyMessage()
                    ->setDataMessageId('1234567'),
                self::DM_BASE_TYPES,
            ],
            'AddDataBoxUser2' => [
                static fn (): AddDataBoxUser2 => new AddDataBoxUser2()
                    ->setDataBoxId('abcdefg')
                    ->setUserInfo(self::createUserInfo()),
                self::DB_TYPES,
            ],
            'ChangeISDSPassword' => [
                static fn (): ChangeISDSPassword => new ChangeISDSPassword()
                    ->setOldPassword('stare-heslo')
                    ->setNewPassword('nove-heslo'),
                self::DB_TYPES,
            ],
            'CheckDataBox' => [
                static fn (): CheckDataBox => new CheckDataBox()
                    ->setDataBoxId('abcdefg'),
                self::DB_TYPES,
            ],
            'DTInfo' => [
                static fn (): DTInfo => new DTInfo()
                    ->setDataBoxId('abcdefg'),
                self::DB_TYPES,
            ],
            'DataBoxCreditInfo' => [
                static fn (): DataBoxCreditInfo => new DataBoxCreditInfo(
                    new DateTimeImmutable('2026-01-01'),
                    new DateTimeImmutable('2026-01-31')
                )->setDataBoxId('abcdefg'),
                self::DB_TYPES,
            ],
            'DeleteDataBoxUser2' => [
                static fn (): DeleteDataBoxUser2 => new DeleteDataBoxUser2()
                    ->setDataBoxId('abcdefg')
                    ->setIsdsId('a23456789012'),
                self::DB_TYPES,
            ],
            'FindDataBox2' => [
                static fn (): FindDataBox2 => new FindDataBox2()
                    ->setOwnerInfo(self::createOwnerInfo()),
                self::DB_TYPES,
            ],
            'GetConstants' => [
                static fn (): GetConstants => new GetConstants()
                    ->setConstDate(new DateTimeImmutable('2026-01-01')),
                self::DB_TYPES,
            ],
            'GetDataBoxActivityStatus' => [
                static fn (): GetDataBoxActivityStatus => new GetDataBoxActivityStatus()
                    ->setDataBoxId('abcdefg')
                    ->setFrom(new DateTimeImmutable('2026-01-01T00:00:00+01:00'))
                    ->setTo(new DateTimeImmutable('2026-01-31T23:59:59+01:00')),
                self::DB_TYPES,
            ],
            'GetDataBoxAddress' => [
                static fn (): GetDataBoxAddress => new GetDataBoxAddress()
                    ->setDataBoxId('abcdefg'),
                self::DB_TYPES,
            ],
            'GetDataBoxList' => [
                static fn (): GetDataBoxList => new GetDataBoxList()
                    ->setType('ALL'),
                self::DB_TYPES,
            ],
            'GetDataBoxUsers2' => [
                static fn (): GetDataBoxUsers2 => new GetDataBoxUsers2()
                    ->setDataBoxId('abcdefg'),
                self::DB_TYPES,
            ],
            'GetOwnerInfoFromLogin' => [
                static fn (): GetOwnerInfoFromLogin => new GetOwnerInfoFromLogin()
                    ->setDummy(''),
                self::DB_TYPES,
            ],
            'GetOwnerInfoFromLogin2' => [
                static fn (): GetOwnerInfoFromLogin2 => new GetOwnerInfoFromLogin2()
                    ->setDummy(''),
                self::DB_TYPES,
            ],
            'GetPasswordInfo' => [
                static fn (): GetPasswordInfo => new GetPasswordInfo()
                    ->setDummy(''),
                self::DB_TYPES,
            ],
            'GetUserInfoFromLogin' => [
                static fn (): GetUserInfoFromLogin => new GetUserInfoFromLogin()
                    ->setDummy(''),
                self::DB_TYPES,
            ],
            'GetUserInfoFromLogin2' => [
                static fn (): GetUserInfoFromLogin2 => new GetUserInfoFromLogin2()
                    ->setDummy(''),
                self::DB_TYPES,
            ],
            'ISDSSearch3' => [
                static fn (): ISDSSearch3 => new ISDSSearch3()
                    ->setSearchText('Testovací úřad')
                    ->setSearchType('GENERAL')
                    ->setSearchScope('ALL')
                    ->setPage(1)
                    ->setPageSize(10)
                    ->setHighlighting(true),
                self::DB_TYPES,
            ],
            'PDZInfo' => [
                static fn (): PDZInfo => new PDZInfo()
                    ->setSender('abcdefg'),
                self::DB_TYPES,
            ],
            'PDZSendInfo' => [
                static fn (): PDZSendInfo => new PDZSendInfo()
                    ->setDataBoxId('abcdefg')
                    ->setType('Normal'),
                self::DB_TYPES,
            ],
            'UpdateDataBoxUser2' => [
                static fn (): UpdateDataBoxUser2 => new UpdateDataBoxUser2()
                    ->setDataBoxId('abcdefg')
                    ->setIsdsId('a23456789012')
                    ->setNewUserInfo(self::createUserInfo()),
                self::DB_TYPES,
            ],
        ];
    }

    /**
     * CreateMultipleMessage with every supported envelope element filled in. The recipient
     * organisational unit belongs to dmRecipient (tRecipients), never to the dmEnvelope of
     * tMultipleMessageEnvelopeSub.
     */
    private static function createFullyPopulatedCreateMessage(): CreateMessage
    {
        $recipient = new Recipient();
        $recipient->setDataBoxId('abcdefg')
            ->setOrgUnit('Odbor X')
            ->setOrgUnitNum(42)
            ->setToHand('Jan Novák');

        $envelope = new Envelope();
        $envelope->setType('K')
            ->setSenderOrgUnit('Podatelna')
            ->setSenderOrgUnitNum(1)
            ->setAnnotation('Testovací zpráva')
            ->setRecipientRefNumber('cj-1')
            ->setSenderRefNumber('cj-2')
            ->setRecipientIdent('sp-1')
            ->setSenderIdent('sp-2')
            ->setLegalTitleLaw(300)
            ->setLegalTitleYear(2008)
            ->setLegalTitleSect('18')
            ->setLegalTitlePar('1')
            ->setLegalTitlePoint('a')
            ->setPersonalDelivery(true)
            ->setAllowSubstDelivery(true)
            ->setOvm(true)
            ->setPublishOwnId(true);

        $file = new File();
        $file->setMimeType('application/pdf')
            ->setMetaType('main')
            ->setDescription('main.pdf')
            ->setEncodedContent(SplFileInfo::createInTemp('obsah'));

        return new CreateMessage()
            ->setEnvelope($envelope)
            ->setFiles([$file])
            ->setRecipients([$recipient]);
    }

    private static function createUserInfo(): UserInfoExt2
    {
        $userInfo = new UserInfoExt2();
        $userInfo->setAifoIsds(false);
        $userInfo->setGivenNames('Jan');
        $userInfo->setLastName('Novák');
        $userInfo->setAdCode('12345678');
        $userInfo->setAdCity('Praha');
        $userInfo->setAdDistrict('Praha 1');
        $userInfo->setAdStreet('Testovací');
        $userInfo->setAdNumberInStreet('1');
        $userInfo->setAdNumberInMunicipality('100');
        $userInfo->setAdZipCode('11000');
        $userInfo->setAdState('CZ');
        $userInfo->setBiDate(new DateTimeImmutable('1980-05-15'));
        $userInfo->setIsdsId('a23456789012');
        $userInfo->setUserType('ENTRUSTED_USER');
        $userInfo->setUserPrivils(255);
        $userInfo->setIc('12345678');
        $userInfo->setFirmName('Testovací firma');
        $userInfo->setCaStreet('Kontaktní 2');
        $userInfo->setCaCity('Brno');
        $userInfo->setCaZipCode('60200');
        $userInfo->setCaState('CZ');

        return $userInfo;
    }

    private static function createOwnerInfo(): OwnerInfoExt2
    {
        $ownerInfo = new OwnerInfoExt2();
        $ownerInfo->setDataBoxId('abcdefg')
            ->setAifoIsds(false)
            ->setDataBoxType('OVM')
            ->setIc('12345678')
            ->setGivenNames('Jan')
            ->setLastName('Novák')
            ->setFirmName('Testovací úřad')
            ->setBiDate(new DateTimeImmutable('1980-05-15'))
            ->setBiCity('Praha')
            ->setBiCounty('Praha')
            ->setBiState('CZ')
            ->setAdCode('12345678')
            ->setAdCity('Praha')
            ->setAdDistrict('Praha 1')
            ->setAdStreet('Testovací')
            ->setAdNumberInStreet('1')
            ->setAdNumberInMunicipality('100')
            ->setAdZipCode('11000')
            ->setAdState('CZ')
            ->setNationality('CZ')
            ->setDataBoxIdOvm('12345678')
            ->setDataBoxState(1)
            ->setOpenAddressing(false)
            ->setUpperDataBoxId('hijklmn');

        return $ownerInfo;
    }

    private static function normalizeToXsdNamespace(string $xml): string
    {
        return str_replace('https://isds.czechpoint.cz/v20', 'http://isds.czechpoint.cz/v20', $xml);
    }
}
