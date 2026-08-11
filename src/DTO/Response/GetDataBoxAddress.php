<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use TomasKulhanek\CzechDataBox\DTO\DataBoxStatus;
use JMS\Serializer\Annotation as Serializer;
use LogicException;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'GetDataBoxAddressResponse')]
class GetDataBoxAddress extends Response
{
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
    #[Serializer\SerializedName('adRegistrationNumber')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $adRegistrationNumber = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('adFullAddress1')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $adFullAddress1 = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('adFullAddress2')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $adFullAddress2 = null;

    /**
     * The tGetAddressOutput type does not define any status element in the XSD.
     * The mapping is kept only defensively in case the server sends dbStatus anyway.
     */
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type(DataBoxStatus::class)]
    #[Serializer\SerializedName('dbStatus')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?ResponseStatus $status = null;

    /**
     * GetDataBoxAddressResponse has no status element defined in the XSD,
     * so this method may throw when the server did not send any dbStatus.
     *
     * @throws LogicException When no dbStatus element was present in the response.
     */
    public function getStatus(): ResponseStatus
    {
        if (!$this->status instanceof ResponseStatus) {
            throw new LogicException('GetDataBoxAddressResponse does not contain any dbStatus element.');
        }
        return $this->status;
    }

    public function hasStatus(): bool
    {
        return $this->status instanceof ResponseStatus;
    }

    public function getAdCode(): ?string
    {
        return $this->adCode;
    }

    public function getAdCity(): ?string
    {
        return $this->adCity;
    }

    public function getAdDistrict(): ?string
    {
        return $this->adDistrict;
    }

    public function getAdStreet(): ?string
    {
        return $this->adStreet;
    }

    public function getAdNumberInStreet(): ?string
    {
        return $this->adNumberInStreet;
    }

    public function getAdNumberInMunicipality(): ?string
    {
        return $this->adNumberInMunicipality;
    }

    public function getAdZipCode(): ?string
    {
        return $this->adZipCode;
    }

    public function getAdState(): ?string
    {
        return $this->adState;
    }

    public function getAdRegistrationNumber(): ?string
    {
        return $this->adRegistrationNumber;
    }

    public function getAdFullAddress1(): ?string
    {
        return $this->adFullAddress1;
    }

    public function getAdFullAddress2(): ?string
    {
        return $this->adFullAddress2;
    }
}
