<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\Traits\DataBoxId;

#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'dbResult')]
#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
class DataBoxResult
{
    use DataBoxId;

    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbType')]
    #[Assert\NotBlank(allowNull: false)]
    protected string $dataBoxType;

    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbName')]
    #[Assert\NotBlank(allowNull: false)]
    protected string $dataBoxName;

    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbAddress')]
    protected ?string $dataBoxAddress = null;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbBiDate')]
    #[Serializer\SkipWhenEmpty]
    protected ?DateTimeImmutable $dataBoxBiDate = null;

    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbICO')]
    #[Serializer\SkipWhenEmpty]
    protected ?string $dataBoxIco = null;

    #[Serializer\Type('bool')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbEffectiveOVM')]
    #[Serializer\SkipWhenEmpty]
    protected ?bool $dataBoxEffectiveOvm = null;

    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbSendOptions')]
    #[Serializer\SkipWhenEmpty]
    protected ?string $dataBoxSendOptions = null;

    public function getDataBoxType(): string
    {
        return $this->dataBoxType;
    }

    public function setDataBoxType(string $dataBoxType): DataBoxResult
    {
        $this->dataBoxType = $dataBoxType;
        return $this;
    }

    public function getDataBoxName(): string
    {
        return $this->dataBoxName;
    }

    public function setDataBoxName(string $dataBoxName): DataBoxResult
    {
        $this->dataBoxName = $dataBoxName;
        return $this;
    }

    public function getDataBoxAddress(): ?string
    {
        return $this->dataBoxAddress;
    }

    public function setDataBoxAddress(?string $dataBoxAddress): DataBoxResult
    {
        $this->dataBoxAddress = $dataBoxAddress;
        return $this;
    }

    public function getDataBoxBiDate(): ?DateTimeImmutable
    {
        return $this->dataBoxBiDate;
    }

    public function setDataBoxBiDate(?DateTimeImmutable $dataBoxBiDate): DataBoxResult
    {
        $this->dataBoxBiDate = $dataBoxBiDate;
        return $this;
    }

    public function getDataBoxIco(): ?string
    {
        return $this->dataBoxIco;
    }

    public function setDataBoxIco(?string $dataBoxIco): DataBoxResult
    {
        $this->dataBoxIco = $dataBoxIco;
        return $this;
    }

    public function getDataBoxEffectiveOvm(): ?bool
    {
        return $this->dataBoxEffectiveOvm;
    }

    public function setDataBoxEffectiveOvm(?bool $dataBoxEffectiveOvm): DataBoxResult
    {
        $this->dataBoxEffectiveOvm = $dataBoxEffectiveOvm;
        return $this;
    }

    public function getDataBoxSendOptions(): ?string
    {
        return $this->dataBoxSendOptions;
    }

    public function setDataBoxSendOptions(?string $dataBoxSendOptions): DataBoxResult
    {
        $this->dataBoxSendOptions = $dataBoxSendOptions;
        return $this;
    }
}
