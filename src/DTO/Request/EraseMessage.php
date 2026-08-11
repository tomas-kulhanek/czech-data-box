<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataMessageId;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'EraseMessage')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['dataMessageId', 'incoming'])]
class EraseMessage implements Request
{
    use DataMessageId;

    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('dmIncoming')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected bool $incoming;

    public function isIncoming(): bool
    {
        return $this->incoming;
    }

    public function setIncoming(bool $incoming): EraseMessage
    {
        $this->incoming = $incoming;
        return $this;
    }
}
