<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;

abstract class PersonInfo
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('pnFirstName')]
    protected ?string $firstName = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('pnMiddleName')]
    protected ?string $middleName = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('pnLastName')]
    protected ?string $lastName = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('pnLastNameAtBirth')]
    protected ?string $lastNameAtBirth = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('adCity')]
    protected ?string $adCity = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('adDistrict')]
    protected ?string $adDistrict = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('adStreet')]
    protected ?string $adStreet = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('adNumberInStreet')]
    protected ?string $adNumberInStreet = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('adNumberInMunicipality')]
    protected ?string $adNumberInMunicipality = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('adZipCode')]
    protected ?string $adZipCode = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('adState')]
    protected ?string $adState = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('adAMCode')]
    protected ?string $adAMCode = null;

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): static
    {
        $this->middleName = $middleName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getLastNameAtBirth(): ?string
    {
        return $this->lastNameAtBirth;
    }

    public function setLastNameAtBirth(?string $lastNameAtBirth): static
    {
        $this->lastNameAtBirth = $lastNameAtBirth;
        return $this;
    }

    public function getAdCity(): ?string
    {
        return $this->adCity;
    }

    public function setAdCity(?string $adCity): static
    {
        $this->adCity = $adCity;
        return $this;
    }

    public function getAdDistrict(): ?string
    {
        return $this->adDistrict;
    }

    public function setAdDistrict(?string $adDistrict): static
    {
        $this->adDistrict = $adDistrict;
        return $this;
    }

    public function getAdStreet(): ?string
    {
        return $this->adStreet;
    }

    public function setAdStreet(?string $adStreet): static
    {
        $this->adStreet = $adStreet;
        return $this;
    }

    public function getAdNumberInStreet(): ?string
    {
        return $this->adNumberInStreet;
    }

    public function setAdNumberInStreet(?string $adNumberInStreet): static
    {
        $this->adNumberInStreet = $adNumberInStreet;
        return $this;
    }

    public function getAdNumberInMunicipality(): ?string
    {
        return $this->adNumberInMunicipality;
    }

    public function setAdNumberInMunicipality(?string $adNumberInMunicipality): static
    {
        $this->adNumberInMunicipality = $adNumberInMunicipality;
        return $this;
    }

    public function getAdZipCode(): ?string
    {
        return $this->adZipCode;
    }

    public function setAdZipCode(?string $adZipCode): static
    {
        $this->adZipCode = $adZipCode;
        return $this;
    }

    public function getAdState(): ?string
    {
        return $this->adState;
    }

    public function setAdState(?string $adState): static
    {
        $this->adState = $adState;
        return $this;
    }

    public function getAdAMCode(): ?string
    {
        return $this->adAMCode;
    }

    public function setAdAMCode(?string $adAMCode): static
    {
        $this->adAMCode = $adAMCode;
        return $this;
    }
}
