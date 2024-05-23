<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Traits;

use JMS\Serializer\Annotation as Serializer;

trait Dummy
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dbDummy')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $dummy = null;

    public function getDummy(): ?string
    {
        return $this->dummy;
    }

    public function setDummy(?string $dummy): self
    {
        $this->dummy = $dummy;
        return $this;
    }
}
