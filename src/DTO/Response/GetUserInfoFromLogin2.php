<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\UserInfoExt2;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'GetUserInfoFromLogin2Response')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['userInfo', 'status'])]
class GetUserInfoFromLogin2 extends DataBoxResponse
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type(UserInfoExt2::class)]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbUserInfo')]
    #[Assert\Valid()]
    protected ?UserInfoExt2 $userInfo = null;

    public function getUserInfo(): ?UserInfoExt2
    {
        return $this->userInfo;
    }
}
