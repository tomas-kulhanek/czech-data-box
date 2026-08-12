<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Serializer\SplFileInfo;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'GetDataBoxListResponse')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['data', 'status'])]
class GetDataBoxList extends DataBoxResponse
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('base64File')]
    #[Serializer\SerializedName('dblData')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?SplFileInfo $data = null;

    public function getData(): ?SplFileInfo
    {
        return $this->data;
    }

    public function setData(?SplFileInfo $data): GetDataBoxList
    {
        $this->data = $data;
        return $this;
    }
}
