<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\OwnerInfoExt2;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'GetOwnerInfoFromLogin2Response')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['ownerInfo', 'status'])]
class GetOwnerInfoFromLogin2 extends DataBoxResponse
{
    #[Serializer\Type(OwnerInfoExt2::class)]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbOwnerInfo')]
    #[Assert\Valid()]
    protected OwnerInfoExt2 $ownerInfo;

    public function getOwnerInfo(): OwnerInfoExt2
    {
        return $this->ownerInfo;
    }
}
