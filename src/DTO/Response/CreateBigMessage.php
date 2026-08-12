<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'CreateBigMessageResponse')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['dataMessageId', 'status'])]
class CreateBigMessage extends DataMessageResponse
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
