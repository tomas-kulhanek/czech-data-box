<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class DTInfo
 */
#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'DTInfo')]
class DTInfo implements Request
{
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbId')]
    protected string $dataBoxId;

    public function getDataBoxId(): string
    {
        return $this->dataBoxId;
    }

    public function setDataBoxId(string $dataBoxId): self
    {
        $this->dataBoxId = $dataBoxId;
        return $this;
    }
}
