<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Obálka velkoobjemové datové zprávy (VoDZ) dle typu tBigMessEnvelope
 * z dmBaseTypes.xsd. Na rozdíl od běžné zprávy má VoDZ jediného příjemce
 * (dbIDRecipient je součástí obálky).
 */
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'dmEnvelope')]
#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
class BigMessageEnvelope
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmType')]
    #[Serializer\XmlAttribute]
    protected ?string $type = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmSenderOrgUnit')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $senderOrgUnit = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\SerializedName('dmSenderOrgUnitNum')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?int $senderOrgUnitNum = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dbIDRecipient')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $recipientId = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmRecipientOrgUnit')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $recipientOrgUnit = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\SerializedName('dmRecipientOrgUnitNum')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?int $recipientOrgUnitNum = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmToHands')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $toHands = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmAnnotation')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\Length(max: 255)]
    protected ?string $annotation = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmRecipientRefNumber')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $recipientRefNumber = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmSenderRefNumber')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $senderRefNumber = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmRecipientIdent')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $recipientIdent = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmSenderIdent')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $senderIdent = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\SerializedName('dmLegalTitleLaw')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?int $legalTitleLaw = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\SerializedName('dmLegalTitleYear')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?int $legalTitleYear = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmLegalTitleSect')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $legalTitleSect = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmLegalTitlePar')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $legalTitlePar = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmLegalTitlePoint')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $legalTitlePoint = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('dmPersonalDelivery')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?bool $personalDelivery = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('dmAllowSubstDelivery')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?bool $allowSubstDelivery = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('dmOVM')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?bool $ovm = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type(PublishOwnId::class)]
    #[Serializer\SerializedName('dmPublishOwnID')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?PublishOwnId $publishOwnId = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): BigMessageEnvelope
    {
        $this->type = $type;
        return $this;
    }

    public function getSenderOrgUnit(): ?string
    {
        return $this->senderOrgUnit;
    }

    public function setSenderOrgUnit(?string $senderOrgUnit): BigMessageEnvelope
    {
        $this->senderOrgUnit = $senderOrgUnit;
        return $this;
    }

    public function getSenderOrgUnitNum(): ?int
    {
        return $this->senderOrgUnitNum;
    }

    public function setSenderOrgUnitNum(?int $senderOrgUnitNum): BigMessageEnvelope
    {
        $this->senderOrgUnitNum = $senderOrgUnitNum;
        return $this;
    }

    public function getRecipientId(): ?string
    {
        return $this->recipientId;
    }

    public function setRecipientId(?string $recipientId): BigMessageEnvelope
    {
        $this->recipientId = $recipientId;
        return $this;
    }

    public function getRecipientOrgUnit(): ?string
    {
        return $this->recipientOrgUnit;
    }

    public function setRecipientOrgUnit(?string $recipientOrgUnit): BigMessageEnvelope
    {
        $this->recipientOrgUnit = $recipientOrgUnit;
        return $this;
    }

    public function getRecipientOrgUnitNum(): ?int
    {
        return $this->recipientOrgUnitNum;
    }

    public function setRecipientOrgUnitNum(?int $recipientOrgUnitNum): BigMessageEnvelope
    {
        $this->recipientOrgUnitNum = $recipientOrgUnitNum;
        return $this;
    }

    public function getToHands(): ?string
    {
        return $this->toHands;
    }

    public function setToHands(?string $toHands): BigMessageEnvelope
    {
        $this->toHands = $toHands;
        return $this;
    }

    public function getAnnotation(): ?string
    {
        return $this->annotation;
    }

    public function setAnnotation(?string $annotation): BigMessageEnvelope
    {
        $this->annotation = $annotation;
        return $this;
    }

    public function getRecipientRefNumber(): ?string
    {
        return $this->recipientRefNumber;
    }

    public function setRecipientRefNumber(?string $recipientRefNumber): BigMessageEnvelope
    {
        $this->recipientRefNumber = $recipientRefNumber;
        return $this;
    }

    public function getSenderRefNumber(): ?string
    {
        return $this->senderRefNumber;
    }

    public function setSenderRefNumber(?string $senderRefNumber): BigMessageEnvelope
    {
        $this->senderRefNumber = $senderRefNumber;
        return $this;
    }

    public function getRecipientIdent(): ?string
    {
        return $this->recipientIdent;
    }

    public function setRecipientIdent(?string $recipientIdent): BigMessageEnvelope
    {
        $this->recipientIdent = $recipientIdent;
        return $this;
    }

    public function getSenderIdent(): ?string
    {
        return $this->senderIdent;
    }

    public function setSenderIdent(?string $senderIdent): BigMessageEnvelope
    {
        $this->senderIdent = $senderIdent;
        return $this;
    }

    public function getLegalTitleLaw(): ?int
    {
        return $this->legalTitleLaw;
    }

    public function setLegalTitleLaw(?int $legalTitleLaw): BigMessageEnvelope
    {
        $this->legalTitleLaw = $legalTitleLaw;
        return $this;
    }

    public function getLegalTitleYear(): ?int
    {
        return $this->legalTitleYear;
    }

    public function setLegalTitleYear(?int $legalTitleYear): BigMessageEnvelope
    {
        $this->legalTitleYear = $legalTitleYear;
        return $this;
    }

    public function getLegalTitleSect(): ?string
    {
        return $this->legalTitleSect;
    }

    public function setLegalTitleSect(?string $legalTitleSect): BigMessageEnvelope
    {
        $this->legalTitleSect = $legalTitleSect;
        return $this;
    }

    public function getLegalTitlePar(): ?string
    {
        return $this->legalTitlePar;
    }

    public function setLegalTitlePar(?string $legalTitlePar): BigMessageEnvelope
    {
        $this->legalTitlePar = $legalTitlePar;
        return $this;
    }

    public function getLegalTitlePoint(): ?string
    {
        return $this->legalTitlePoint;
    }

    public function setLegalTitlePoint(?string $legalTitlePoint): BigMessageEnvelope
    {
        $this->legalTitlePoint = $legalTitlePoint;
        return $this;
    }

    public function getPersonalDelivery(): ?bool
    {
        return $this->personalDelivery;
    }

    public function setPersonalDelivery(?bool $personalDelivery): BigMessageEnvelope
    {
        $this->personalDelivery = $personalDelivery;
        return $this;
    }

    public function getAllowSubstDelivery(): ?bool
    {
        return $this->allowSubstDelivery;
    }

    public function setAllowSubstDelivery(?bool $allowSubstDelivery): BigMessageEnvelope
    {
        $this->allowSubstDelivery = $allowSubstDelivery;
        return $this;
    }

    public function getOvm(): ?bool
    {
        return $this->ovm;
    }

    public function setOvm(?bool $ovm): BigMessageEnvelope
    {
        $this->ovm = $ovm;
        return $this;
    }

    public function getPublishOwnId(): ?PublishOwnId
    {
        return $this->publishOwnId;
    }

    public function setPublishOwnId(PublishOwnId|bool|null $publishOwnId): BigMessageEnvelope
    {
        if (is_bool($publishOwnId)) {
            $publishOwnId = new PublishOwnId()->setValue($publishOwnId);
        }
        $this->publishOwnId = $publishOwnId;
        return $this;
    }
}
