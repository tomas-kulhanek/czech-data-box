<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\OwnerInfoExt2;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'FindDataBox2Response')]
class FindDataBox2 extends Response
{
    use DataBoxStatus;

    /**
     * @var OwnerInfoExt2[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\OwnerInfoExt2>')]
    #[Serializer\XmlList(entry: 'dbOwnerInfo', inline: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbResults')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\All([
        new Assert\Type(type: OwnerInfoExt2::class)
    ])]
    #[Assert\Valid()]
    protected array $result = [];

    /**
     * @return OwnerInfoExt2[]
     */
    public function getResult(): array
    {
        return $this->result;
    }
}
