<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'NewAccessData2')]
#[Serializer\AccessorOrder(order: 'custom', custom: [
    'dataBoxId',
    'isdsId',
    'feePaid',
    'virtual',
    'email',
    'approved',
    'externRefNumber',
])]
class NewAccessData2 extends DataBoxManagementRequest
{
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('isdsID')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Assert\NotBlank(allowNull: false)]
    protected string $isdsId;

    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('dbFeePaid')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected bool $feePaid = false;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('dbVirtual')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?bool $virtual = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('email')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $email = null;

    public function getIsdsId(): string
    {
        return $this->isdsId;
    }

    public function setIsdsId(string $isdsId): NewAccessData2
    {
        $this->isdsId = $isdsId;
        return $this;
    }

    public function getFeePaid(): bool
    {
        return $this->feePaid;
    }

    public function setFeePaid(bool $feePaid): NewAccessData2
    {
        $this->feePaid = $feePaid;
        return $this;
    }

    public function getVirtual(): ?bool
    {
        return $this->virtual;
    }

    public function setVirtual(?bool $virtual): NewAccessData2
    {
        $this->virtual = $virtual;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): NewAccessData2
    {
        $this->email = $email;
        return $this;
    }
}
