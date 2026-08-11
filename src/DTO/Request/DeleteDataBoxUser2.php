<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\Traits\DataBoxId;
use TomasKulhanek\CzechDataBox\Traits\ExtApproval;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'DeleteDataBoxUser2')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['dataBoxId', 'isdsId', 'approved', 'externRefNumber'])]
class DeleteDataBoxUser2 implements Request
{
    use DataBoxId;
    use ExtApproval;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('isdsID')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\NotBlank(allowNull: false)]
    protected string $isdsId;

    public function getIsdsId(): string
    {
        return $this->isdsId;
    }

    public function setIsdsId(string $isdsId): DeleteDataBoxUser2
    {
        $this->isdsId = $isdsId;
        return $this;
    }
}
