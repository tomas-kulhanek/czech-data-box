<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\PersonalOwnerInfo;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'FindPersonalDataBox')]
class FindPersonalDataBox implements IRequest
{
    #[Serializer\Type(PersonalOwnerInfo::class)]
    #[Serializer\SerializedName('dbOwnerInfo')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\Valid()]
    protected PersonalOwnerInfo $ownerInfo;

    public function getOwnerInfo(): PersonalOwnerInfo
    {
        return $this->ownerInfo;
    }

    public function setOwnerInfo(PersonalOwnerInfo $ownerInfo): FindPersonalDataBox
    {
        $this->ownerInfo = $ownerInfo;
        return $this;
    }
}
