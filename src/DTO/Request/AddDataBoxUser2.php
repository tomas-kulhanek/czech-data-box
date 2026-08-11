<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\UserInfoExt2;
use TomasKulhanek\CzechDataBox\Traits\DataBoxId;
use TomasKulhanek\CzechDataBox\Traits\ExtApproval;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'AddDataBoxUser2')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['dataBoxId', 'userInfo', 'virtual', 'email', 'approved', 'externRefNumber'])]
class AddDataBoxUser2 implements Request
{
    use DataBoxId;
    use ExtApproval;

    #[Serializer\Type(UserInfoExt2::class)]
    #[Serializer\SerializedName('dbUserInfo')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Assert\Valid()]
    protected UserInfoExt2 $userInfo;

    /**
     * True, pokud se přístupové údaje (pouze pro interní uživatele) nemají posílat, ale má se použít virtuální obálka.
     */
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('dbVirtual')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?bool $virtual = null;

    /**
     * V případě použití virtuální obálky email, na nějž má přijít odkaz na Aktivační portál.
     */
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('email')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $email = null;

    public function getUserInfo(): UserInfoExt2
    {
        return $this->userInfo;
    }

    public function setUserInfo(UserInfoExt2 $userInfo): AddDataBoxUser2
    {
        $this->userInfo = $userInfo;
        return $this;
    }

    public function getVirtual(): ?bool
    {
        return $this->virtual;
    }

    public function setVirtual(?bool $virtual): AddDataBoxUser2
    {
        $this->virtual = $virtual;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): AddDataBoxUser2
    {
        $this->email = $email;
        return $this;
    }
}
