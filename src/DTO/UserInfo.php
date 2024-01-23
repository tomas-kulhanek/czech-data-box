<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\Address;
use TomasKulhanek\CzechDataBox\Traits\PersonName;

#[Serializer\XmlRoot(name: 'p:dbUserInfo')]
#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
class UserInfo
{
    use PersonName;
    use Address;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\XmlElement(cdata: false)]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\SerializedName('p:biDate')]
    protected ?DateTimeImmutable $biDate = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false)]
    #[Serializer\SerializedName('p:userID')]
    protected ?string $userId = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false)]
    #[Serializer\SerializedName('p:userType')]
    protected ?string $userType = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\XmlElement(cdata: false)]
    #[Serializer\SerializedName('p:userPrivils')]
    protected ?int $userPrivils = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false)]
    #[Serializer\SerializedName('p:ic')]
    protected ?string $ic = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false)]
    #[Serializer\SerializedName('p:firmName')]
    protected ?string $firmName = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false)]
    #[Serializer\SerializedName('p:caStreet')]
    protected ?string $caStreet = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false)]
    #[Serializer\SerializedName('p:caCity')]
    protected ?string $caCity = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false)]
    #[Serializer\SerializedName('p:caZipCode')]
    protected ?string $caZipCode = null;

    public function getBiDate(): ?DateTimeImmutable
    {
        return $this->biDate;
    }

    public function setBiDate(?DateTimeImmutable $biDate): UserInfo
    {
        $this->biDate = $biDate;
        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): UserInfo
    {
        $this->userId = $userId;
        return $this;
    }

    public function getUserType(): ?string
    {
        return $this->userType;
    }

    public function setUserType(?string $userType): UserInfo
    {
        $this->userType = $userType;
        return $this;
    }

    public function getUserPrivils(): ?int
    {
        return $this->userPrivils;
    }

    public function setUserPrivils(?int $userPrivils): UserInfo
    {
        $this->userPrivils = $userPrivils;
        return $this;
    }

    public function getIc(): ?string
    {
        return $this->ic;
    }

    public function setIc(?string $ic): UserInfo
    {
        $this->ic = $ic;
        return $this;
    }

    public function getFirmName(): ?string
    {
        return $this->firmName;
    }

    public function setFirmName(?string $firmName): UserInfo
    {
        $this->firmName = $firmName;
        return $this;
    }

    public function getCaStreet(): ?string
    {
        return $this->caStreet;
    }

    public function setCaStreet(?string $caStreet): UserInfo
    {
        $this->caStreet = $caStreet;
        return $this;
    }

    public function getCaCity(): ?string
    {
        return $this->caCity;
    }

    public function setCaCity(?string $caCity): UserInfo
    {
        $this->caCity = $caCity;
        return $this;
    }

    public function getCaZipCode(): ?string
    {
        return $this->caZipCode;
    }

    public function setCaZipCode(?string $caZipCode): UserInfo
    {
        $this->caZipCode = $caZipCode;
        return $this;
    }

    public function getAdCity(): ?string
    {
        return $this->adCity;
    }

    public function setAdCity(?string $adCity): UserInfo
    {
        $this->adCity = $adCity;
        return $this;
    }

    public function getAdStreet(): ?string
    {
        return $this->adStreet;
    }

    public function setAdStreet(?string $adStreet): UserInfo
    {
        $this->adStreet = $adStreet;
        return $this;
    }

    public function getAdNumberInStreet(): ?string
    {
        return $this->adNumberInStreet;
    }

    public function setAdNumberInStreet(?string $adNumberInStreet): UserInfo
    {
        $this->adNumberInStreet = $adNumberInStreet;
        return $this;
    }

    public function getAdNumberInMunicipality(): ?string
    {
        return $this->adNumberInMunicipality;
    }

    public function setAdNumberInMunicipality(?string $adNumberInMunicipality): UserInfo
    {
        $this->adNumberInMunicipality = $adNumberInMunicipality;
        return $this;
    }

    public function getAdZipCode(): ?string
    {
        return $this->adZipCode;
    }

    public function setAdZipCode(?string $adZipCode): UserInfo
    {
        $this->adZipCode = $adZipCode;
        return $this;
    }

    public function getAdState(): ?string
    {
        return $this->adState;
    }

    public function setAdState(?string $adState): UserInfo
    {
        $this->adState = $adState;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): UserInfo
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): UserInfo
    {
        $this->middleName = $middleName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): UserInfo
    {
        $this->lastName = $lastName;
        return $this;
    }
}
