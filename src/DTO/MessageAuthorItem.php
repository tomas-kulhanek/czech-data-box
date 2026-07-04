<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'maItem')]
#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
class MessageAuthorItem
{
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('key')]
    #[Serializer\XmlAttribute]
    protected string $key;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('value')]
    #[Serializer\XmlAttribute]
    protected string $value;

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): MessageAuthorItem
    {
        $this->key = $key;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): MessageAuthorItem
    {
        $this->value = $value;
        return $this;
    }
}
