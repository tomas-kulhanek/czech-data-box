<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'GetDataBoxList')]
class GetDataBoxList implements IRequest
{
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dblType')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected string $type;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): GetDataBoxList
    {
        $this->type = $type;
        return $this;
    }
}
