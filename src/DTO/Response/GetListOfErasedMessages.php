<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'GetListOfErasedMessagesResponse')]
class GetListOfErasedMessages extends Response
{
    use DataMessageStatus;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('asyncID')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $asyncId = null;

    public function getAsyncId(): ?string
    {
        return $this->asyncId;
    }

    public function setAsyncId(?string $asyncId): GetListOfErasedMessages
    {
        $this->asyncId = $asyncId;
        return $this;
    }
}
