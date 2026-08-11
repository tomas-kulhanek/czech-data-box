<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'dbUserInfo')]
#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\AccessorOrder(order: 'custom', custom: [
    'biDate',
    'userId',
    'userType',
    'userPrivils',
    'ic',
    'firmName',
    'caStreet',
    'caCity',
    'caZipCode',
    'caState',
    'firstName',
    'middleName',
    'lastName',
    'lastNameAtBirth',
    'adCity',
    'adDistrict',
    'adStreet',
    'adNumberInStreet',
    'adNumberInMunicipality',
    'adZipCode',
    'adState',
    'adAMCode',
])]
class UserInfo extends PersonInfo
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\SerializedName('biDate')]
    protected ?DateTimeImmutable $biDate = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('userID')]
    protected ?string $userId = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('userType')]
    protected ?string $userType = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('userPrivils')]
    protected ?int $userPrivils = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('ic')]
    protected ?string $ic = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('firmName')]
    protected ?string $firmName = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('caStreet')]
    protected ?string $caStreet = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('caCity')]
    protected ?string $caCity = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('caZipCode')]
    protected ?string $caZipCode = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('caState')]
    protected ?string $caState = null;

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

    public function getCaState(): ?string
    {
        return $this->caState;
    }

    public function setCaState(?string $caState): UserInfo
    {
        $this->caState = $caState;
        return $this;
    }
}
