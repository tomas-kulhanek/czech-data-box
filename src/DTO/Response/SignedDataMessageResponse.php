<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Serializer\SplFileInfo;

abstract class SignedDataMessageResponse extends DataMessageResponse
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('base64File')]
    #[Serializer\SerializedName('dmSignature')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?SplFileInfo $signature = null;

    public function getSignature(): ?SplFileInfo
    {
        return $this->signature;
    }

    public function setSignature(?SplFileInfo $signature): static
    {
        $this->signature = $signature;
        return $this;
    }
}
