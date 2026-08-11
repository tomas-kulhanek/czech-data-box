<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'dmEnvelope')]
#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
class Envelope
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmType')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\XmlAttribute]
    protected ?string $type = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmSenderOrgUnit')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $senderOrgUnit = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\SerializedName('dmSenderOrgUnitNum')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?int $senderOrgUnitNum = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmAnnotation')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $annotation = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmRecipientRefNumber')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $recipientRefNumber = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmSenderRefNumber')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $senderRefNumber = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmRecipientIdent')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $recipientIdent = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmSenderIdent')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $senderIdent = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\SerializedName('dmLegalTitleLaw')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?int $legalTitleLaw = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\SerializedName('dmLegalTitleYear')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?int $legalTitleYear = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmLegalTitleSect')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $legalTitleSect = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmLegalTitlePar')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $legalTitlePar = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmLegalTitlePoint')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $legalTitlePoint = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('dmPersonalDelivery')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?bool $personalDelivery = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('dmAllowSubstDelivery')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?bool $allowSubstDelivery = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('dmOVM')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?bool $ovm = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type(PublishOwnId::class)]
    #[Serializer\SerializedName('dmPublishOwnID')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?PublishOwnId $publishOwnId = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): Envelope
    {
        $this->type = $type;
        return $this;
    }

    public function getSenderOrgUnit(): ?string
    {
        return $this->senderOrgUnit;
    }

    public function setSenderOrgUnit(?string $senderOrgUnit): Envelope
    {
        $this->senderOrgUnit = $senderOrgUnit;
        return $this;
    }

    public function getSenderOrgUnitNum(): ?int
    {
        return $this->senderOrgUnitNum;
    }

    public function setSenderOrgUnitNum(?int $senderOrgUnitNum): Envelope
    {
        $this->senderOrgUnitNum = $senderOrgUnitNum;
        return $this;
    }

    public function getAnnotation(): ?string
    {
        return $this->annotation;
    }

    public function setAnnotation(?string $annotation): Envelope
    {
        $this->annotation = $annotation;
        return $this;
    }

    public function getRecipientRefNumber(): ?string
    {
        return $this->recipientRefNumber;
    }

    public function setRecipientRefNumber(?string $recipientRefNumber): Envelope
    {
        $this->recipientRefNumber = $recipientRefNumber;
        return $this;
    }

    public function getSenderRefNumber(): ?string
    {
        return $this->senderRefNumber;
    }

    public function setSenderRefNumber(?string $senderRefNumber): Envelope
    {
        $this->senderRefNumber = $senderRefNumber;
        return $this;
    }

    public function getRecipientIdent(): ?string
    {
        return $this->recipientIdent;
    }

    public function setRecipientIdent(?string $recipientIdent): Envelope
    {
        $this->recipientIdent = $recipientIdent;
        return $this;
    }

    public function getSenderIdent(): ?string
    {
        return $this->senderIdent;
    }

    public function setSenderIdent(?string $senderIdent): Envelope
    {
        $this->senderIdent = $senderIdent;
        return $this;
    }

    public function getLegalTitleLaw(): ?int
    {
        return $this->legalTitleLaw;
    }

    public function setLegalTitleLaw(?int $legalTitleLaw): Envelope
    {
        $this->legalTitleLaw = $legalTitleLaw;
        return $this;
    }

    public function getLegalTitleYear(): ?int
    {
        return $this->legalTitleYear;
    }

    public function setLegalTitleYear(?int $legalTitleYear): Envelope
    {
        $this->legalTitleYear = $legalTitleYear;
        return $this;
    }

    public function getLegalTitleSect(): ?string
    {
        return $this->legalTitleSect;
    }

    public function setLegalTitleSect(?string $legalTitleSect): Envelope
    {
        $this->legalTitleSect = $legalTitleSect;
        return $this;
    }

    public function getLegalTitlePar(): ?string
    {
        return $this->legalTitlePar;
    }

    public function setLegalTitlePar(?string $legalTitlePar): Envelope
    {
        $this->legalTitlePar = $legalTitlePar;
        return $this;
    }

    public function getLegalTitlePoint(): ?string
    {
        return $this->legalTitlePoint;
    }

    public function setLegalTitlePoint(?string $legalTitlePoint): Envelope
    {
        $this->legalTitlePoint = $legalTitlePoint;
        return $this;
    }

    public function getPersonalDelivery(): ?bool
    {
        return $this->personalDelivery;
    }

    public function setPersonalDelivery(?bool $personalDelivery): Envelope
    {
        $this->personalDelivery = $personalDelivery;
        return $this;
    }

    public function getAllowSubstDelivery(): ?bool
    {
        return $this->allowSubstDelivery;
    }

    public function setAllowSubstDelivery(?bool $allowSubstDelivery): Envelope
    {
        $this->allowSubstDelivery = $allowSubstDelivery;
        return $this;
    }

    /**
     * Schránky typu FO, PO a PFO, které mají povoleno vystupovat jako OVM (podle § 5a) musejí již při vytváření DZ určit, v jakém režimu (OVM x ne-OVM) odesílají. Význam to má z procesních (a účetních) důvodů.
     *
     * @return bool|null
     */
    public function getOvm(): ?bool
    {
        return $this->ovm;
    }

    /**
     * Schránky typu FO, PO a PFO, které mají povoleno vystupovat jako OVM (podle § 5a) musejí již při vytváření DZ určit, v jakém režimu (OVM x ne-OVM) odesílají. Význam to má z procesních (a účetních) důvodů.
     *
     * @return Envelope
     */
    public function setOvm(?bool $ovm): Envelope
    {
        $this->ovm = $ovm;
        return $this;
    }

    public function getPublishOwnId(): ?PublishOwnId
    {
        return $this->publishOwnId;
    }

    public function setPublishOwnId(PublishOwnId|bool|null $publishOwnId): Envelope
    {
        if (is_bool($publishOwnId)) {
            $publishOwnId = new PublishOwnId()->setValue($publishOwnId);
        }
        $this->publishOwnId = $publishOwnId;
        return $this;
    }
}
