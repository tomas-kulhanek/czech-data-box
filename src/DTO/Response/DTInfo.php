<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

/**
 * Class DTInfo
 */
#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'DTInfoResponse')]
class DTInfo extends IResponse
{
    use DataBoxStatus;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('ActDTType')]
    protected ?int $actDtType = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('ActDTCapacity')]
    protected ?int $actDtCapacity = null;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('ActDTFrom')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?DateTimeImmutable $actDtFrom = null;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('ActDTTo')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?DateTimeImmutable $actDtTo = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('ActDTCapUsed')]
    protected ?int $actDtCapUsed = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('FutDTType')]
    protected ?int $futDtType = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('FutDTCapacity')]
    protected ?int $futDtCapacity = null;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('FutDTFrom')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?DateTimeImmutable $futDtFrom = null;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('FutDTTo')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?DateTimeImmutable $futDtTo = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('FutDTPaid')]
    protected ?int $futDtPaid = null;

    public function getActDtType(): ?int
    {
        return $this->actDtType;
    }

    public function getActDtCapacity(): ?int
    {
        return $this->actDtCapacity;
    }

    public function getActDtFrom(): ?DateTimeImmutable
    {
        return $this->actDtFrom;
    }

    public function getActDtTo(): ?DateTimeImmutable
    {
        return $this->actDtTo;
    }

    public function getActDtCapUsed(): ?int
    {
        return $this->actDtCapUsed;
    }

    public function getFutDtType(): ?int
    {
        return $this->futDtType;
    }

    public function getFutDtCapacity(): ?int
    {
        return $this->futDtCapacity;
    }

    public function getFutDtFrom(): ?DateTimeImmutable
    {
        return $this->futDtFrom;
    }

    public function getFutDtTo(): ?DateTimeImmutable
    {
        return $this->futDtTo;
    }

    public function getFutDtPaid(): ?int
    {
        return $this->futDtPaid;
    }

    public function isCredit(): bool
    {
        return $this->futDtType === 1;
    }

    public function isFlatRate(): bool
    {
        return $this->futDtType === 3;
    }

    public function isAction(): bool
    {
        return $this->futDtType === 4;
    }

    public function isFutureCredit(): bool
    {
        return $this->futDtType === 1;
    }

    public function isFutureFlatRate(): bool
    {
        return $this->futDtType === 3;
    }

    public function isFutureAction(): bool
    {
        return $this->futDtType === 4;
    }
}
