<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\OwnerInfo;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'FindDataBoxResponse')]
class FindDataBox extends IResponse
{
    use DataBoxStatus;

    /**
     * @var OwnerInfo[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\OwnerInfo>')]
    #[Serializer\XmlList(entry: 'dbOwnerInfo', inline: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbResults')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\All([
        new Assert\Type(type: OwnerInfo::class)
    ])]
    #[Assert\Valid()]
    protected array $result = [];

    /**
     * @return OwnerInfo[]
     */
    public function getResult(): array
    {
        return $this->result;
    }
}
