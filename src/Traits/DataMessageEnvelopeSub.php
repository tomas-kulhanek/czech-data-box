<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Traits;

use JMS\Serializer\Annotation as Serializer;

trait DataMessageEnvelopeSub
{
    /**
     * @Serializer\Type("string")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:dmSenderOrgUnit")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?string $senderOrgUnit = null;

    /**
     * @Serializer\Type("int")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:dmSenderOrgUnitNum")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?int $senderOrgUnitNum = null;

    /**
     * @Serializer\Type("string")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:dmRecipientOrgUnit")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?string $recipientOrgUnit = null;

    /**
     * @Serializer\Type("int")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:dmRecipientOrgUnitNum")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?int $recipientOrgUnitNum = null;

    /**
     * @Serializer\Type("string")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:dmAnnotation")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?string $annotation = null;

    /**
     * @Serializer\Type("string")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:dmRecipientRefNumber")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?string $recipientRefNumber = null;

    /**
     * @Serializer\Type("string")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:dmSenderRefNumber")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?string $senderRefNumber = null;

    /**
     * @Serializer\Type("string")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:dmRecipientIdent")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?string $recipientIdent = null;

    /**
     * @Serializer\Type("string")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:dmSenderIdent")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?string $senderIdent = null;

    /**
     * @Serializer\Type("int")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:dmLegalTitleLaw")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?int $legalTitleLaw = null;

    /**
     * @Serializer\Type("int")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:dmLegalTitleYear")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?int $legalTitleYear = null;

    /**
     * @Serializer\Type("string")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:dmLegalTitleSect")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?string $legalTitleSect = null;

    /**
     * @Serializer\Type("string")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:dmLegalTitlePar")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?string $legalTitlePar = null;

    /**
     * @Serializer\Type("string")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:dmLegalTitlePoint")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?string $legalTitlePoint = null;

    /**
     * @Serializer\Type("bool")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:dmPersonalDelivery")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?bool $personalDelivery = null;

    /**
     * @Serializer\Type("bool")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:dmAllowSubstDelivery")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?bool $allowSubstDelivery = null;

    public function getSenderOrgUnit(): ?string
    {
        return $this->senderOrgUnit;
    }

    public function setSenderOrgUnit(?string $senderOrgUnit): self
    {
        $this->senderOrgUnit = $senderOrgUnit;
        return $this;
    }

    public function getSenderOrgUnitNum(): ?int
    {
        return $this->senderOrgUnitNum;
    }

    public function setSenderOrgUnitNum(?int $senderOrgUnitNum): self
    {
        $this->senderOrgUnitNum = $senderOrgUnitNum;
        return $this;
    }

    public function getRecipientOrgUnit(): ?string
    {
        return $this->recipientOrgUnit;
    }

    public function setRecipientOrgUnit(?string $recipientOrgUnit): self
    {
        $this->recipientOrgUnit = $recipientOrgUnit;
        return $this;
    }

    public function getRecipientOrgUnitNum(): ?int
    {
        return $this->recipientOrgUnitNum;
    }

    public function setRecipientOrgUnitNum(?int $recipientOrgUnitNum): self
    {
        $this->recipientOrgUnitNum = $recipientOrgUnitNum;
        return $this;
    }

    public function getAnnotation(): ?string
    {
        return $this->annotation;
    }

    public function setAnnotation(?string $annotation): self
    {
        $this->annotation = $annotation;
        return $this;
    }

    public function getRecipientRefNumber(): ?string
    {
        return $this->recipientRefNumber;
    }

    public function setRecipientRefNumber(?string $recipientRefNumber): self
    {
        $this->recipientRefNumber = $recipientRefNumber;
        return $this;
    }

    public function getSenderRefNumber(): ?string
    {
        return $this->senderRefNumber;
    }

    public function setSenderRefNumber(?string $senderRefNumber): self
    {
        $this->senderRefNumber = $senderRefNumber;
        return $this;
    }

    public function getRecipientIdent(): ?string
    {
        return $this->recipientIdent;
    }

    public function setRecipientIdent(?string $recipientIdent): self
    {
        $this->recipientIdent = $recipientIdent;
        return $this;
    }

    public function getSenderIdent(): ?string
    {
        return $this->senderIdent;
    }

    public function setSenderIdent(?string $senderIdent): self
    {
        $this->senderIdent = $senderIdent;
        return $this;
    }

    public function getLegalTitleLaw(): ?int
    {
        return $this->legalTitleLaw;
    }

    public function setLegalTitleLaw(?int $legalTitleLaw): self
    {
        $this->legalTitleLaw = $legalTitleLaw;
        return $this;
    }

    public function getLegalTitleYear(): ?int
    {
        return $this->legalTitleYear;
    }

    public function setLegalTitleYear(?int $legalTitleYear): self
    {
        $this->legalTitleYear = $legalTitleYear;
        return $this;
    }

    public function getLegalTitleSect(): ?string
    {
        return $this->legalTitleSect;
    }

    public function setLegalTitleSect(?string $legalTitleSect): self
    {
        $this->legalTitleSect = $legalTitleSect;
        return $this;
    }

    public function getLegalTitlePar(): ?string
    {
        return $this->legalTitlePar;
    }

    public function setLegalTitlePar(?string $legalTitlePar): self
    {
        $this->legalTitlePar = $legalTitlePar;
        return $this;
    }

    public function getLegalTitlePoint(): ?string
    {
        return $this->legalTitlePoint;
    }

    public function setLegalTitlePoint(?string $legalTitlePoint): self
    {
        $this->legalTitlePoint = $legalTitlePoint;
        return $this;
    }

    public function getPersonalDelivery(): ?bool
    {
        return $this->personalDelivery;
    }

    public function setPersonalDelivery(?bool $personalDelivery): self
    {
        $this->personalDelivery = $personalDelivery;
        return $this;
    }

    public function getAllowSubstDelivery(): ?bool
    {
        return $this->allowSubstDelivery;
    }

    public function setAllowSubstDelivery(?bool $allowSubstDelivery): self
    {
        $this->allowSubstDelivery = $allowSubstDelivery;
        return $this;
    }
}
