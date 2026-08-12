<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'AddDataBoxUser2Response')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['dataBoxId', 'accessDataId', 'status'])]
class AddDataBoxUser2 extends DataBoxResponse
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dbID')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $dataBoxId = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dbAccessDataId')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $accessDataId = null;

    public function getDataBoxId(): ?string
    {
        return $this->dataBoxId;
    }

    public function getAccessDataId(): ?string
    {
        return $this->accessDataId;
    }
}
