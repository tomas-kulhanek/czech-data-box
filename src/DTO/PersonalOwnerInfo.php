<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataBoxId;
use TomasKulhanek\CzechDataBox\Traits\PersonName;

#[Serializer\XmlRoot(name: 'p:dbOwnerInfo')]
#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
class PersonalOwnerInfo
{
    use DataBoxId;
    use PersonName;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('aifoIsds')]
    protected ?bool $aifoIsds = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\SerializedName('biDate')]
    protected ?DateTimeImmutable $biDate = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('biCity')]
    protected ?string $biCity = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('biCounty')]
    protected ?string $biCounty = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('biState')]
    protected ?string $biState = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('adCode')]
    protected ?int $adCode = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('adCity')]
    protected ?string $adCity = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('adStreet')]
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
    #[Serializer\SerializedName('nationality')]
    protected ?string $nationality = null;

    public function getAifoIsds(): ?bool
    {
        return $this->aifoIsds;
    }

    public function setAifoIsds(?bool $aifoIsds): PersonalOwnerInfo
    {
        $this->aifoIsds = $aifoIsds;
        return $this;
    }

    public function getBiDate(): ?DateTimeImmutable
    {
        return $this->biDate;
    }

    public function setBiDate(?DateTimeImmutable $biDate): PersonalOwnerInfo
    {
        $this->biDate = $biDate;
        return $this;
    }

    public function getBiCity(): ?string
    {
        return $this->biCity;
    }

    public function setBiCity(?string $biCity): PersonalOwnerInfo
    {
        $this->biCity = $biCity;
        return $this;
    }

    public function getBiCounty(): ?string
    {
        return $this->biCounty;
    }

    public function setBiCounty(?string $biCounty): PersonalOwnerInfo
    {
        $this->biCounty = $biCounty;
        return $this;
    }

    public function getBiState(): ?string
    {
        return $this->biState;
    }

    public function setBiState(?string $biState): PersonalOwnerInfo
    {
        $this->biState = $biState;
        return $this;
    }

    public function getAdCode(): ?int
    {
        return $this->adCode;
    }

    public function setAdCode(?int $adCode): PersonalOwnerInfo
    {
        $this->adCode = $adCode;
        return $this;
    }

    public function getAdCity(): ?string
    {
        return $this->adCity;
    }

    public function setAdCity(?string $adCity): PersonalOwnerInfo
    {
        $this->adCity = $adCity;
        return $this;
    }

    public function getAdDistrict(): ?string
    {
        return $this->adDistrict;
    }

    public function setAdDistrict(?string $adDistrict): PersonalOwnerInfo
    {
        $this->adDistrict = $adDistrict;
        return $this;
    }

    public function getAdStreet(): ?string
    {
        return $this->adStreet;
    }

    public function setAdStreet(?string $adStreet): PersonalOwnerInfo
    {
        $this->adStreet = $adStreet;
        return $this;
    }

    public function getAdNumberInStreet(): ?string
    {
        return $this->adNumberInStreet;
    }

    public function setAdNumberInStreet(?string $adNumberInStreet): PersonalOwnerInfo
    {
        $this->adNumberInStreet = $adNumberInStreet;
        return $this;
    }

    public function getAdNumberInMunicipality(): ?string
    {
        return $this->adNumberInMunicipality;
    }

    public function setAdNumberInMunicipality(?string $adNumberInMunicipality): PersonalOwnerInfo
    {
        $this->adNumberInMunicipality = $adNumberInMunicipality;
        return $this;
    }

    public function getAdZipCode(): ?string
    {
        return $this->adZipCode;
    }

    public function setAdZipCode(?string $adZipCode): PersonalOwnerInfo
    {
        $this->adZipCode = $adZipCode;
        return $this;
    }

    public function getAdState(): ?string
    {
        return $this->adState;
    }

    public function setAdState(?string $adState): PersonalOwnerInfo
    {
        $this->adState = $adState;
        return $this;
    }

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(?string $nationality): PersonalOwnerInfo
    {
        $this->nationality = $nationality;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): PersonalOwnerInfo
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): PersonalOwnerInfo
    {
        $this->middleName = $middleName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): PersonalOwnerInfo
    {
        $this->lastName = $lastName;
        return $this;
    }
}
