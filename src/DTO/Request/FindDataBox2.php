<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\OwnerInfoExt2;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'FindDataBox2')]
class FindDataBox2 implements Request
{
    #[Serializer\Type(OwnerInfoExt2::class)]
    #[Serializer\SerializedName('dbOwnerInfo')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\Valid()]
    protected OwnerInfoExt2 $ownerInfo;

    public function getOwnerInfo(): OwnerInfoExt2
    {
        return $this->ownerInfo;
    }

    public function setOwnerInfo(OwnerInfoExt2 $ownerInfo): FindDataBox2
    {
        $this->ownerInfo = $ownerInfo;
        return $this;
    }
}
