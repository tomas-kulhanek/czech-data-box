<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'PickUpAsyncResponseResponse')]
class PickUpAsyncResponse extends Response
{
    use DataMessageStatus;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('asyncReqType')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $asyncReqType = null;

    /**
     * Base64 encoded content of the asynchronous response (xs:base64Binary).
     */
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('asyncResponse')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $asyncResponse = null;

    public function getAsyncReqType(): ?string
    {
        return $this->asyncReqType;
    }

    public function setAsyncReqType(?string $asyncReqType): PickUpAsyncResponse
    {
        $this->asyncReqType = $asyncReqType;
        return $this;
    }

    public function getAsyncResponse(): ?string
    {
        return $this->asyncResponse;
    }

    public function setAsyncResponse(?string $asyncResponse): PickUpAsyncResponse
    {
        $this->asyncResponse = $asyncResponse;
        return $this;
    }
}
