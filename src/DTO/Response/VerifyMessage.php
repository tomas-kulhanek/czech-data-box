<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\Hash;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'VerifyMessageResponse')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['hash', 'status'])]
class VerifyMessage extends DataMessageResponse
{
    #[Serializer\Type(Hash::class)]
    #[Serializer\SerializedName('dmHash')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Assert\Valid()]
    protected ?Hash $hash = null;

    public function getHash(): ?Hash
    {
        return $this->hash;
    }

    public function setHash(?Hash $hash): VerifyMessage
    {
        $this->hash = $hash;
        return $this;
    }
}
