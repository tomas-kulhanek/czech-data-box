<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\UserInfoExt2;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'GetDataBoxUsers2Response')]
class GetDataBoxUsers2 extends Response
{
    use DataBoxStatus;

    /**
     * @var UserInfoExt2[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\UserInfoExt2>')]
    #[Serializer\XmlList(entry: 'dbUserInfo', inline: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbUsers')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\All([
        new Assert\Type(type: UserInfoExt2::class)
    ])]
    #[Assert\Valid()]
    protected array $users = [];

    /**
     * @return UserInfoExt2[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }
}
