<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\UserInfo;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'GetUserInfoFromLoginResponse')]
class GetUserInfoFromLogin extends Response
{
    use DataBoxStatus;

    #[Serializer\Type(UserInfo::class)]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbUserInfo')]
    #[Assert\Valid()]
    protected UserInfo $userInfo;

    public function getUserInfo(): UserInfo
    {
        return $this->userInfo;
    }
}
