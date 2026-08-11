<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataMessageId;

#[Serializer\AccessorOrder(order: 'custom', custom: [
    'dataMessageId',
    'senderId',
    'sender',
    'senderAddress',
    'senderType',
    'recipient',
    'recipientAddress',
    'ambiguousRecipient',
    'senderOrgUnit',
    'senderOrgUnitNum',
    'recipientId',
    'recipientOrgUnit',
    'recipientOrgUnitNum',
    'toHands',
    'annotation',
    'recipientRefNumber',
    'senderRefNumber',
    'recipientIdent',
    'senderIdent',
    'legalTitleLaw',
    'legalTitleYear',
    'legalTitleSect',
    'legalTitlePar',
    'legalTitlePoint',
    'personalDelivery',
    'allowSubstDelivery',
])]
abstract class AbstractMessageEnvelope
{
    use DataMessageId;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dbIDSender')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $senderId = null;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmSender')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $sender = null;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmSenderAddress')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SkipWhenEmpty]
    protected ?string $senderAddress = null;

    #[Serializer\Type('int')]
    #[Serializer\SerializedName('dmSenderType')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?int $senderType = null;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmRecipient')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $recipient = null;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmRecipientAddress')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SkipWhenEmpty]
    protected ?string $recipientAddress = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('dmAmbiguousRecipient')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?bool $ambiguousRecipient = null;

    #[Serializer\Type('string')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('dmSenderOrgUnit')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $senderOrgUnit = null;

    #[Serializer\Type('int')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('dmSenderOrgUnitNum')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?int $senderOrgUnitNum = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dbIDRecipient')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $recipientId = null;

    #[Serializer\Type('string')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('dmRecipientOrgUnit')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $recipientOrgUnit = null;

    #[Serializer\Type('int')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('dmRecipientOrgUnitNum')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?int $recipientOrgUnitNum = null;

    #[Serializer\Type('string')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('dmToHands')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $toHands = null;

    #[Serializer\Type('string')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('dmAnnotation')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $annotation = null;

    #[Serializer\Type('string')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('dmRecipientRefNumber')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $recipientRefNumber = null;

    #[Serializer\Type('string')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('dmSenderRefNumber')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $senderRefNumber = null;

    #[Serializer\Type('string')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('dmRecipientIdent')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $recipientIdent = null;

    #[Serializer\Type('string')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('dmSenderIdent')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $senderIdent = null;

    #[Serializer\Type('int')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('dmLegalTitleLaw')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?int $legalTitleLaw = null;

    #[Serializer\Type('int')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('dmLegalTitleYear')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?int $legalTitleYear = null;

    #[Serializer\Type('string')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('dmLegalTitleSect')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $legalTitleSect = null;

    #[Serializer\Type('string')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('dmLegalTitlePar')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $legalTitlePar = null;

    #[Serializer\Type('string')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('dmLegalTitlePoint')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $legalTitlePoint = null;

    #[Serializer\Type('bool')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('dmPersonalDelivery')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?bool $personalDelivery = null;

    #[Serializer\Type('bool')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('dmAllowSubstDelivery')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?bool $allowSubstDelivery = null;

    public function getSenderId(): ?string
    {
        return $this->senderId;
    }

    public function setSenderId(?string $senderId): static
    {
        $this->senderId = $senderId;
        return $this;
    }

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSender(?string $sender): static
    {
        $this->sender = $sender;
        return $this;
    }

    public function getSenderAddress(): ?string
    {
        return $this->senderAddress;
    }

    public function setSenderAddress(?string $senderAddress): static
    {
        $this->senderAddress = $senderAddress;
        return $this;
    }

    public function getSenderType(): ?int
    {
        return $this->senderType;
    }

    public function setSenderType(?int $senderType): static
    {
        $this->senderType = $senderType;
        return $this;
    }

    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    public function setRecipient(?string $recipient): static
    {
        $this->recipient = $recipient;
        return $this;
    }

    public function getRecipientAddress(): ?string
    {
        return $this->recipientAddress;
    }

    public function setRecipientAddress(?string $recipientAddress): static
    {
        $this->recipientAddress = $recipientAddress;
        return $this;
    }

    public function getAmbiguousRecipient(): ?bool
    {
        return $this->ambiguousRecipient;
    }

    public function setAmbiguousRecipient(?bool $ambiguousRecipient): static
    {
        $this->ambiguousRecipient = $ambiguousRecipient;
        return $this;
    }

    public function getSenderOrgUnit(): ?string
    {
        return $this->senderOrgUnit;
    }

    public function setSenderOrgUnit(?string $senderOrgUnit): static
    {
        $this->senderOrgUnit = $senderOrgUnit;
        return $this;
    }

    public function getSenderOrgUnitNum(): ?int
    {
        return $this->senderOrgUnitNum;
    }

    public function setSenderOrgUnitNum(?int $senderOrgUnitNum): static
    {
        $this->senderOrgUnitNum = $senderOrgUnitNum;
        return $this;
    }

    public function getRecipientId(): ?string
    {
        return $this->recipientId;
    }

    public function setRecipientId(?string $recipientId): static
    {
        $this->recipientId = $recipientId;
        return $this;
    }

    public function getRecipientOrgUnit(): ?string
    {
        return $this->recipientOrgUnit;
    }

    public function setRecipientOrgUnit(?string $recipientOrgUnit): static
    {
        $this->recipientOrgUnit = $recipientOrgUnit;
        return $this;
    }

    public function getRecipientOrgUnitNum(): ?int
    {
        return $this->recipientOrgUnitNum;
    }

    public function setRecipientOrgUnitNum(?int $recipientOrgUnitNum): static
    {
        $this->recipientOrgUnitNum = $recipientOrgUnitNum;
        return $this;
    }

    public function getToHands(): ?string
    {
        return $this->toHands;
    }

    public function setToHands(?string $toHands): static
    {
        $this->toHands = $toHands;
        return $this;
    }

    public function getAnnotation(): ?string
    {
        return $this->annotation;
    }

    public function setAnnotation(?string $annotation): static
    {
        $this->annotation = $annotation;
        return $this;
    }

    public function getRecipientRefNumber(): ?string
    {
        return $this->recipientRefNumber;
    }

    public function setRecipientRefNumber(?string $recipientRefNumber): static
    {
        $this->recipientRefNumber = $recipientRefNumber;
        return $this;
    }

    public function getSenderRefNumber(): ?string
    {
        return $this->senderRefNumber;
    }

    public function setSenderRefNumber(?string $senderRefNumber): static
    {
        $this->senderRefNumber = $senderRefNumber;
        return $this;
    }

    public function getRecipientIdent(): ?string
    {
        return $this->recipientIdent;
    }

    public function setRecipientIdent(?string $recipientIdent): static
    {
        $this->recipientIdent = $recipientIdent;
        return $this;
    }

    public function getSenderIdent(): ?string
    {
        return $this->senderIdent;
    }

    public function setSenderIdent(?string $senderIdent): static
    {
        $this->senderIdent = $senderIdent;
        return $this;
    }

    public function getLegalTitleLaw(): ?int
    {
        return $this->legalTitleLaw;
    }

    public function setLegalTitleLaw(?int $legalTitleLaw): static
    {
        $this->legalTitleLaw = $legalTitleLaw;
        return $this;
    }

    public function getLegalTitleYear(): ?int
    {
        return $this->legalTitleYear;
    }

    public function setLegalTitleYear(?int $legalTitleYear): static
    {
        $this->legalTitleYear = $legalTitleYear;
        return $this;
    }

    public function getLegalTitleSect(): ?string
    {
        return $this->legalTitleSect;
    }

    public function setLegalTitleSect(?string $legalTitleSect): static
    {
        $this->legalTitleSect = $legalTitleSect;
        return $this;
    }

    public function getLegalTitlePar(): ?string
    {
        return $this->legalTitlePar;
    }

    public function setLegalTitlePar(?string $legalTitlePar): static
    {
        $this->legalTitlePar = $legalTitlePar;
        return $this;
    }

    public function getLegalTitlePoint(): ?string
    {
        return $this->legalTitlePoint;
    }

    public function setLegalTitlePoint(?string $legalTitlePoint): static
    {
        $this->legalTitlePoint = $legalTitlePoint;
        return $this;
    }

    public function getPersonalDelivery(): ?bool
    {
        return $this->personalDelivery;
    }

    public function setPersonalDelivery(?bool $personalDelivery): static
    {
        $this->personalDelivery = $personalDelivery;
        return $this;
    }

    public function getAllowSubstDelivery(): ?bool
    {
        return $this->allowSubstDelivery;
    }

    public function setAllowSubstDelivery(?bool $allowSubstDelivery): static
    {
        $this->allowSubstDelivery = $allowSubstDelivery;
        return $this;
    }
}
