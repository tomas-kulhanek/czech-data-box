<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;

abstract class DataMessageRequest implements Request
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmID')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $dataMessageId = null;

    public function getDataMessageId(): ?string
    {
        return $this->dataMessageId;
    }

    public function setDataMessageId(?string $dataMessageId): static
    {
        $this->dataMessageId = $dataMessageId;
        return $this;
    }
}
