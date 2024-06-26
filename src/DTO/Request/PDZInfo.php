<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'PDZInfo')]
class PDZInfo implements IRequest
{
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('PDZSender')]
    protected string $sender;

    public function getSender(): string
    {
        return $this->sender;
    }

    public function setSender(string $sender): PDZInfo
    {
        $this->sender = $sender;
        return $this;
    }
}
