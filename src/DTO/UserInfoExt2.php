<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'dbUserInfo')]
#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
class UserInfoExt2
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('aifoIsds')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?bool $aifoIsds = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('pnGivenNames')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $givenNames = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('pnLastName')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $lastName = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('adCode')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $adCode = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('adCity')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $adCity = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('adDistrict')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
<<<<<<<< HEAD:src/DTO/UserInfoExt2.php
========
    #[Serializer\SerializedName('adDistrict')]
>>>>>>>> origin/main:src/DTO/PersonalOwnerInfo.php
    protected ?string $adDistrict = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('adStreet')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $adStreet = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('adNumberInStreet')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $adNumberInStreet = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('adNumberInMunicipality')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $adNumberInMunicipality = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('adZipCode')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $adZipCode = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('adState')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $adState = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\SerializedName('biDate')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?DateTimeImmutable $biDate = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('isdsID')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $isdsId = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('userType')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $userType = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\SerializedName('userPrivils')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?int $userPrivils = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('ic')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $ic = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('firmName')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $firmName = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('caStreet')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $caStreet = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('caCity')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $caCity = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('caZipCode')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $caZipCode = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('caState')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $caState = null;

    public function getAifoIsds(): ?bool
    {
        return $this->aifoIsds;
    }

    public function setAifoIsds(?bool $aifoIsds): UserInfoExt2
    {
        $this->aifoIsds = $aifoIsds;
        return $this;
    }

    public function getGivenNames(): ?string
    {
        return $this->givenNames;
    }

    public function setGivenNames(?string $givenNames): UserInfoExt2
    {
        $this->givenNames = $givenNames;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): UserInfoExt2
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getAdCode(): ?string
    {
        return $this->adCode;
    }

    public function setAdCode(?string $adCode): UserInfoExt2
    {
        $this->adCode = $adCode;
        return $this;
    }

    public function getAdCity(): ?string
    {
        return $this->adCity;
    }

    public function setAdCity(?string $adCity): UserInfoExt2
    {
        $this->adCity = $adCity;
        return $this;
    }

    public function getAdDistrict(): ?string
    {
        return $this->adDistrict;
    }

    public function setAdDistrict(?string $adDistrict): UserInfoExt2
    {
        $this->adDistrict = $adDistrict;
        return $this;
    }

    public function getAdStreet(): ?string
    {
        return $this->adStreet;
    }

    public function setAdStreet(?string $adStreet): UserInfoExt2
    {
        $this->adStreet = $adStreet;
        return $this;
    }

    public function getAdNumberInStreet(): ?string
    {
        return $this->adNumberInStreet;
    }

    public function setAdNumberInStreet(?string $adNumberInStreet): UserInfoExt2
    {
        $this->adNumberInStreet = $adNumberInStreet;
        return $this;
    }

    public function getAdNumberInMunicipality(): ?string
    {
        return $this->adNumberInMunicipality;
    }

    public function setAdNumberInMunicipality(?string $adNumberInMunicipality): UserInfoExt2
    {
        $this->adNumberInMunicipality = $adNumberInMunicipality;
        return $this;
    }

    public function getAdZipCode(): ?string
    {
        return $this->adZipCode;
    }

    public function setAdZipCode(?string $adZipCode): UserInfoExt2
    {
        $this->adZipCode = $adZipCode;
        return $this;
    }

    public function getAdState(): ?string
    {
        return $this->adState;
    }

    public function setAdState(?string $adState): UserInfoExt2
    {
        $this->adState = $adState;
        return $this;
    }

    public function getBiDate(): ?DateTimeImmutable
    {
        return $this->biDate;
    }

    public function setBiDate(?DateTimeImmutable $biDate): UserInfoExt2
    {
        $this->biDate = $biDate;
        return $this;
    }

    public function getIsdsId(): ?string
    {
        return $this->isdsId;
    }

    public function setIsdsId(?string $isdsId): UserInfoExt2
    {
        $this->isdsId = $isdsId;
        return $this;
    }

    public function getUserType(): ?string
    {
        return $this->userType;
    }

    public function setUserType(?string $userType): UserInfoExt2
    {
        $this->userType = $userType;
        return $this;
    }

    public function getUserPrivils(): ?int
    {
        return $this->userPrivils;
    }

    public function setUserPrivils(?int $userPrivils): UserInfoExt2
    {
        $this->userPrivils = $userPrivils;
        return $this;
    }

    public function getIc(): ?string
    {
        return $this->ic;
    }

    public function setIc(?string $ic): UserInfoExt2
    {
        $this->ic = $ic;
        return $this;
    }

    public function getFirmName(): ?string
    {
        return $this->firmName;
    }

    public function setFirmName(?string $firmName): UserInfoExt2
    {
        $this->firmName = $firmName;
        return $this;
    }

    public function getCaStreet(): ?string
    {
        return $this->caStreet;
    }

    public function setCaStreet(?string $caStreet): UserInfoExt2
    {
        $this->caStreet = $caStreet;
        return $this;
    }

    public function getCaCity(): ?string
    {
        return $this->caCity;
    }

    public function setCaCity(?string $caCity): UserInfoExt2
    {
        $this->caCity = $caCity;
        return $this;
    }

    public function getCaZipCode(): ?string
    {
        return $this->caZipCode;
    }

    public function setCaZipCode(?string $caZipCode): UserInfoExt2
    {
        $this->caZipCode = $caZipCode;
        return $this;
    }

    public function getCaState(): ?string
    {
        return $this->caState;
    }

    public function setCaState(?string $caState): UserInfoExt2
    {
        $this->caState = $caState;
        return $this;
    }
}
