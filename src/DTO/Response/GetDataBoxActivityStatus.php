<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\Period;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'GetDataBoxActivityStatusResponse')]
class GetDataBoxActivityStatus extends DataBoxResponse
{
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbID')]
    protected ?string $dataBoxId = null;

    /**
     * @var Period[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\Period>')]
    #[Serializer\XmlList(entry: 'Period', inline: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('Periods')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Assert\All([
        new Assert\Type(type: Period::class)
    ])]
    #[Assert\Valid()]
    protected array $period = [];

    public function getDataBoxId(): ?string
    {
        return $this->dataBoxId;
    }

    public function setDataBoxId(?string $dataBoxId): GetDataBoxActivityStatus
    {
        $this->dataBoxId = $dataBoxId;
        return $this;
    }

    /**
     * @return Period[]
     */
    public function getPeriod(): array
    {
        return $this->period;
    }
}
