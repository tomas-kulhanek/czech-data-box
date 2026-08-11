<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'PickUpAsyncResponse')]
class PickUpAsyncResponse implements Request
{
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('asyncID')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected string $asyncId;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('asyncReqType')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected string $asyncReqType;

    public function getAsyncId(): string
    {
        return $this->asyncId;
    }

    public function setAsyncId(string $asyncId): PickUpAsyncResponse
    {
        $this->asyncId = $asyncId;
        return $this;
    }

    public function getAsyncReqType(): string
    {
        return $this->asyncReqType;
    }

    public function setAsyncReqType(string $asyncReqType): PickUpAsyncResponse
    {
        $this->asyncReqType = $asyncReqType;
        return $this;
    }
}
