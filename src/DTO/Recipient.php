<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'dmRecipient')]
#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
class Recipient
{
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbIDRecipient')]
    #[Assert\NotBlank(allowNull: false)]
    protected string $dataBoxId;

    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmRecipientOrgUnit')]
    #[Serializer\SkipWhenEmpty]
    protected ?string $orgUnit = null;

    #[Serializer\Type('int')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmRecipientOrgUnitNum')]
    protected ?int $orgUnitNum = null;

    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmToHands')]
    protected string $toHand = '';

    public function getDataBoxId(): string
    {
        return $this->dataBoxId;
    }

    public function setDataBoxId(string $dataBoxId): Recipient
    {
        $this->dataBoxId = $dataBoxId;
        return $this;
    }

    public function getOrgUnit(): ?string
    {
        return $this->orgUnit;
    }

    public function setOrgUnit(?string $orgUnit): Recipient
    {
        $this->orgUnit = $orgUnit;
        return $this;
    }

    public function getOrgUnitNum(): ?int
    {
        return $this->orgUnitNum;
    }

    public function setOrgUnitNum(?int $orgUnitNum): Recipient
    {
        $this->orgUnitNum = $orgUnitNum;
        return $this;
    }

    public function getToHand(): string
    {
        return $this->toHand;
    }

    public function setToHand(string $toHand): Recipient
    {
        $this->toHand = $toHand;
        return $this;
    }
}
