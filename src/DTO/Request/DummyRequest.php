<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;

abstract class DummyRequest implements Request
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dbDummy')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $dummy = null;

    public function getDummy(): ?string
    {
        return $this->dummy;
    }

    public function setDummy(?string $dummy): static
    {
        $this->dummy = $dummy;
        return $this;
    }
}
