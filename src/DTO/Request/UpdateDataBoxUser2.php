<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\UserInfoExt2;
use TomasKulhanek\CzechDataBox\Traits\DataBoxId;
use TomasKulhanek\CzechDataBox\Traits\ExtApproval;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'UpdateDataBoxUser2')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['dataBoxId', 'isdsId', 'newUserInfo', 'approved', 'externRefNumber'])]
class UpdateDataBoxUser2 implements Request
{
    use DataBoxId;
    use ExtApproval;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('isdsID')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Assert\NotBlank(allowNull: false)]
    protected string $isdsId;

    #[Serializer\Type(UserInfoExt2::class)]
    #[Serializer\SerializedName('dbNewUserInfo')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Assert\Valid()]
    protected UserInfoExt2 $newUserInfo;

    public function getIsdsId(): string
    {
        return $this->isdsId;
    }

    public function setIsdsId(string $isdsId): UpdateDataBoxUser2
    {
        $this->isdsId = $isdsId;
        return $this;
    }

    public function getNewUserInfo(): UserInfoExt2
    {
        return $this->newUserInfo;
    }

    public function setNewUserInfo(UserInfoExt2 $newUserInfo): UpdateDataBoxUser2
    {
        $this->newUserInfo = $newUserInfo;
        return $this;
    }
}
