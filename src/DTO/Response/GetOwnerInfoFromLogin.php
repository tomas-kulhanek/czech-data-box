<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\OwnerInfo;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'GetOwnerInfoFromLoginResponse')]
class GetOwnerInfoFromLogin extends IResponse
{
    use DataBoxStatus;

    #[Serializer\Type(OwnerInfo::class)]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbOwnerInfo')]
    #[Assert\Valid()]
    protected OwnerInfo $ownerInfo;

    public function getOwnerInfo(): OwnerInfo
    {
        return $this->ownerInfo;
    }
}
