<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\UserInfo;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'GetUserInfoFromLoginResponse')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['userInfo', 'status'])]
class GetUserInfoFromLogin extends DataBoxResponse
{
    #[Serializer\Type(UserInfo::class)]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbUserInfo')]
    #[Assert\Valid()]
    protected ?UserInfo $userInfo = null;

    public function getUserInfo(): ?UserInfo
    {
        return $this->userInfo;
    }
}
