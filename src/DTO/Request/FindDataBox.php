<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\OwnerInfo;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'FindDataBox')]
class FindDataBox implements IRequest
{
    #[Serializer\Type(OwnerInfo::class)]
    #[Serializer\SerializedName('dbOwnerInfo')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\Valid()]
    protected OwnerInfo $ownerInfo;

    public function getOwnerInfo(): OwnerInfo
    {
        return $this->ownerInfo;
    }

    public function setOwnerInfo(OwnerInfo $ownerInfo): FindDataBox
    {
        $this->ownerInfo = $ownerInfo;
        return $this;
    }
}
