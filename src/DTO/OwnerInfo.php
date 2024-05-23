<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\Address;
use TomasKulhanek\CzechDataBox\Traits\DataBoxId;
use TomasKulhanek\CzechDataBox\Traits\PersonName;

#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'dbOwnerInfo')]
#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
class OwnerInfo
{
    use DataBoxId;
    use PersonName;
    use Address;

    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbType')]
    protected ?string $dataBoxType = null;

    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('ic')]
    #[Serializer\SkipWhenEmpty]
    protected ?string $ic = null;

    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('pnLastNameAtBirth')]
    #[Serializer\SkipWhenEmpty]
    protected ?string $lastNameAtBirth = null;

    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('firmName')]
    #[Serializer\SkipWhenEmpty]
    protected ?string $firmName = null;

    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\SerializedName('biDate')]
    #[Serializer\SkipWhenEmpty]
    protected ?DateTimeImmutable $biDate = null;

    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('biCity')]
    #[Serializer\SkipWhenEmpty]
    protected ?string $biCity = null;

    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('biCounty')]
    #[Serializer\SkipWhenEmpty]
    protected ?string $biCounty = null;

    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('biState')]
    #[Serializer\SkipWhenEmpty]
    protected ?string $biState = null;

    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('nationality')]
    #[Serializer\SkipWhenEmpty]
    protected ?string $nationality = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('email')]
    protected ?string $email = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('telNumber')]
    protected ?string $telNumber = null;

    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('identifier')]
    #[Serializer\SkipWhenEmpty]
    protected ?string $identifier = null;

    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('registryCode')]
    #[Serializer\SkipWhenEmpty]
    protected ?string $registryCode = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbState')]
    protected ?int $dataBoxState = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbEffectiveOVM')]
    protected ?bool $dataBoxEffectiveOvm = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbOpenAddressing')]
    protected ?bool $dataBoxOpenAddressing = null;

    public function getDataBoxType(): ?string
    {
        return $this->dataBoxType;
    }

    public function setDataBoxType(string $dataBoxType): OwnerInfo
    {
        $this->dataBoxType = $dataBoxType;
        return $this;
    }

    public function getIc(): ?string
    {
        return $this->ic;
    }

    public function setIc(?string $ic): OwnerInfo
    {
        $this->ic = $ic;
        return $this;
    }

    public function getLastNameAtBirth(): ?string
    {
        return $this->lastNameAtBirth;
    }

    public function setLastNameAtBirth(?string $lastNameAtBirth): OwnerInfo
    {
        $this->lastNameAtBirth = $lastNameAtBirth;
        return $this;
    }

    public function getFirmName(): ?string
    {
        return $this->firmName;
    }

    public function setFirmName(?string $firmName): OwnerInfo
    {
        $this->firmName = $firmName;
        return $this;
    }

    public function getBiDate(): ?DateTimeImmutable
    {
        return $this->biDate;
    }

    public function setBiDate(?DateTimeImmutable $biDate): OwnerInfo
    {
        $this->biDate = $biDate;
        return $this;
    }

    public function getBiCity(): ?string
    {
        return $this->biCity;
    }

    public function setBiCity(?string $biCity): OwnerInfo
    {
        $this->biCity = $biCity;
        return $this;
    }

    public function getBiCounty(): ?string
    {
        return $this->biCounty;
    }

    public function setBiCounty(?string $biCounty): OwnerInfo
    {
        $this->biCounty = $biCounty;
        return $this;
    }

    public function getBiState(): ?string
    {
        return $this->biState;
    }

    public function setBiState(?string $biState): OwnerInfo
    {
        $this->biState = $biState;
        return $this;
    }

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(?string $nationality): OwnerInfo
    {
        $this->nationality = $nationality;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): OwnerInfo
    {
        $this->email = $email;
        return $this;
    }

    public function getTelNumber(): ?string
    {
        return $this->telNumber;
    }

    public function setTelNumber(?string $telNumber): OwnerInfo
    {
        $this->telNumber = $telNumber;
        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): OwnerInfo
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getRegistryCode(): ?string
    {
        return $this->registryCode;
    }

    public function setRegistryCode(?string $registryCode): OwnerInfo
    {
        $this->registryCode = $registryCode;
        return $this;
    }

    public function getDataBoxState(): ?int
    {
        return $this->dataBoxState;
    }

    public function setDataBoxState(?int $dataBoxState): OwnerInfo
    {
        $this->dataBoxState = $dataBoxState;
        return $this;
    }

    public function getDataBoxEffectiveOvm(): ?bool
    {
        return $this->dataBoxEffectiveOvm;
    }

    public function setDataBoxEffectiveOvm(?bool $dataBoxEffectiveOvm): OwnerInfo
    {
        $this->dataBoxEffectiveOvm = $dataBoxEffectiveOvm;
        return $this;
    }

    public function getDataBoxOpenAddressing(): ?bool
    {
        return $this->dataBoxOpenAddressing;
    }

    public function setDataBoxOpenAddressing(?bool $dataBoxOpenAddressing): OwnerInfo
    {
        $this->dataBoxOpenAddressing = $dataBoxOpenAddressing;
        return $this;
    }
}
