<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'dbOwnerInfo')]
#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
class OwnerInfoExt2
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dbID')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $dataBoxId = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('aifoIsds')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?bool $aifoIsds = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dbType')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $dataBoxType = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('ic')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $ic = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('pnGivenNames')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $givenNames = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('pnLastName')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $lastName = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('firmName')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $firmName = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\SerializedName('biDate')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?DateTimeImmutable $biDate = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('biCity')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $biCity = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('biCounty')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $biCounty = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('biState')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $biState = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('adCode')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $adCode = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('adCity')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $adCity = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('adDistrict')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $adDistrict = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('adStreet')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $adStreet = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('adNumberInStreet')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $adNumberInStreet = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('adNumberInMunicipality')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $adNumberInMunicipality = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('adZipCode')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $adZipCode = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('adState')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $adState = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('nationality')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $nationality = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dbIdOVM')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $dataBoxIdOvm = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\SerializedName('dbState')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?int $dataBoxState = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('dbOpenAddressing')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?bool $openAddressing = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dbUpperID')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $upperDataBoxId = null;

    public function getDataBoxId(): ?string
    {
        return $this->dataBoxId;
    }

    public function setDataBoxId(?string $dataBoxId): OwnerInfoExt2
    {
        $this->dataBoxId = $dataBoxId;
        return $this;
    }

    public function getAifoIsds(): ?bool
    {
        return $this->aifoIsds;
    }

    public function setAifoIsds(?bool $aifoIsds): OwnerInfoExt2
    {
        $this->aifoIsds = $aifoIsds;
        return $this;
    }

    public function getDataBoxType(): ?string
    {
        return $this->dataBoxType;
    }

    public function setDataBoxType(?string $dataBoxType): OwnerInfoExt2
    {
        $this->dataBoxType = $dataBoxType;
        return $this;
    }

    public function getIc(): ?string
    {
        return $this->ic;
    }

    public function setIc(?string $ic): OwnerInfoExt2
    {
        $this->ic = $ic;
        return $this;
    }

    public function getGivenNames(): ?string
    {
        return $this->givenNames;
    }

    public function setGivenNames(?string $givenNames): OwnerInfoExt2
    {
        $this->givenNames = $givenNames;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): OwnerInfoExt2
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getFirmName(): ?string
    {
        return $this->firmName;
    }

    public function setFirmName(?string $firmName): OwnerInfoExt2
    {
        $this->firmName = $firmName;
        return $this;
    }

    public function getBiDate(): ?DateTimeImmutable
    {
        return $this->biDate;
    }

    public function setBiDate(?DateTimeImmutable $biDate): OwnerInfoExt2
    {
        $this->biDate = $biDate;
        return $this;
    }

    public function getBiCity(): ?string
    {
        return $this->biCity;
    }

    public function setBiCity(?string $biCity): OwnerInfoExt2
    {
        $this->biCity = $biCity;
        return $this;
    }

    public function getBiCounty(): ?string
    {
        return $this->biCounty;
    }

    public function setBiCounty(?string $biCounty): OwnerInfoExt2
    {
        $this->biCounty = $biCounty;
        return $this;
    }

    public function getBiState(): ?string
    {
        return $this->biState;
    }

    public function setBiState(?string $biState): OwnerInfoExt2
    {
        $this->biState = $biState;
        return $this;
    }

    public function getAdCode(): ?string
    {
        return $this->adCode;
    }

    public function setAdCode(?string $adCode): OwnerInfoExt2
    {
        $this->adCode = $adCode;
        return $this;
    }

    public function getAdCity(): ?string
    {
        return $this->adCity;
    }

    public function setAdCity(?string $adCity): OwnerInfoExt2
    {
        $this->adCity = $adCity;
        return $this;
    }

    public function getAdDistrict(): ?string
    {
        return $this->adDistrict;
    }

    public function setAdDistrict(?string $adDistrict): OwnerInfoExt2
    {
        $this->adDistrict = $adDistrict;
        return $this;
    }

    public function getAdStreet(): ?string
    {
        return $this->adStreet;
    }

    public function setAdStreet(?string $adStreet): OwnerInfoExt2
    {
        $this->adStreet = $adStreet;
        return $this;
    }

    public function getAdNumberInStreet(): ?string
    {
        return $this->adNumberInStreet;
    }

    public function setAdNumberInStreet(?string $adNumberInStreet): OwnerInfoExt2
    {
        $this->adNumberInStreet = $adNumberInStreet;
        return $this;
    }

    public function getAdNumberInMunicipality(): ?string
    {
        return $this->adNumberInMunicipality;
    }

    public function setAdNumberInMunicipality(?string $adNumberInMunicipality): OwnerInfoExt2
    {
        $this->adNumberInMunicipality = $adNumberInMunicipality;
        return $this;
    }

    public function getAdZipCode(): ?string
    {
        return $this->adZipCode;
    }

    public function setAdZipCode(?string $adZipCode): OwnerInfoExt2
    {
        $this->adZipCode = $adZipCode;
        return $this;
    }

    public function getAdState(): ?string
    {
        return $this->adState;
    }

    public function setAdState(?string $adState): OwnerInfoExt2
    {
        $this->adState = $adState;
        return $this;
    }

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(?string $nationality): OwnerInfoExt2
    {
        $this->nationality = $nationality;
        return $this;
    }

    public function getDataBoxIdOvm(): ?string
    {
        return $this->dataBoxIdOvm;
    }

    public function setDataBoxIdOvm(?string $dataBoxIdOvm): OwnerInfoExt2
    {
        $this->dataBoxIdOvm = $dataBoxIdOvm;
        return $this;
    }

    public function getDataBoxState(): ?int
    {
        return $this->dataBoxState;
    }

    public function setDataBoxState(?int $dataBoxState): OwnerInfoExt2
    {
        $this->dataBoxState = $dataBoxState;
        return $this;
    }

    public function getOpenAddressing(): ?bool
    {
        return $this->openAddressing;
    }

    public function setOpenAddressing(?bool $openAddressing): OwnerInfoExt2
    {
        $this->openAddressing = $openAddressing;
        return $this;
    }

    public function getUpperDataBoxId(): ?string
    {
        return $this->upperDataBoxId;
    }

    public function setUpperDataBoxId(?string $upperDataBoxId): OwnerInfoExt2
    {
        $this->upperDataBoxId = $upperDataBoxId;
        return $this;
    }
}
