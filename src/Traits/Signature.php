<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Traits;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\Serializer\Utils\SplFileInfo;

trait Signature
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('base64File')]
    #[Serializer\SerializedName('dmSignature')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?SplFileInfo $signature = null;

    public function getSignature(): ?SplFileInfo
    {
        return $this->signature;
    }

    public function setSignature(SplFileInfo $signature): self
    {
        $this->signature = $signature;
        return $this;
    }
}
