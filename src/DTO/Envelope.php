<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataMessageEnvelopeSub;

#[Serializer\XmlRoot(name: 'p:dmEnvelope')]
#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\AccessorOrder(custom: ['senderOrgUnit', 'senderOrgUnitNum', 'annotation', 'recipientRefNumber', 'senderRefNumber', 'recipientIdent', 'senderIdent', 'legalTitleLaw', 'legalTitleYear', 'legalTitleSect', 'legalTitlePar', 'legalTitlePoint', 'personalDelivery', 'allowSubstDelivery', 'ovm', 'publishOwnId'])]
class Envelope
{
	use DataMessageEnvelopeSub;

	#[Serializer\Type('string')]
	#[Serializer\SkipWhenEmpty]
	#[Serializer\SerializedName('dmType')]
	#[Serializer\XmlElement(cdata: false)]
	#[Serializer\XmlAttribute]
	protected ?string $type = null;

	#[Serializer\Type('bool')]
	#[Serializer\SkipWhenEmpty]
	#[Serializer\SerializedName('p:dmAllowSubstDelivery')]
	#[Serializer\XmlElement(cdata: false)]
	protected ?bool $allowSubstDelivery = null;

	#[Serializer\SkipWhenEmpty]
	#[Serializer\Type('bool')]
	#[Serializer\SerializedName('p:dmOVM')]
	#[Serializer\XmlElement(cdata: false)]
	protected ?bool $ovm = null;

	#[Serializer\SkipWhenEmpty]
	#[Serializer\Type('bool')]
	#[Serializer\SerializedName('p:dmPublishOwnID')]
	#[Serializer\XmlElement(cdata: false)]
	protected ?bool $publishOwnId = null;

	public function getType(): ?string
	{
		return $this->type;
	}

	public function setType(string $type): Envelope
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
	 * @param bool|null $ovm
	 * @return Envelope
	 */
	public function setOvm(?bool $ovm): Envelope
	{
		$this->ovm = $ovm;
		return $this;
	}

	public function getPublishOwnId(): ?bool
	{
		return $this->publishOwnId;
	}

	public function setPublishOwnId(?bool $publishOwnId): Envelope
	{
		$this->publishOwnId = $publishOwnId;
		return $this;
	}

	public function getRecipientOrgUnit(): ?string
	{
		return $this->recipientOrgUnit;
	}

	public function setRecipientOrgUnit(?string $recipientOrgUnit): Envelope
	{
		$this->recipientOrgUnit = $recipientOrgUnit;
		return $this;
	}

	public function getRecipientOrgUnitNum(): ?int
	{
		return $this->recipientOrgUnitNum;
	}

	public function setRecipientOrgUnitNum(?int $recipientOrgUnitNum): Envelope
	{
		$this->recipientOrgUnitNum = $recipientOrgUnitNum;
		return $this;
	}
}
