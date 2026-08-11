<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Traits;

use JMS\Serializer\Annotation as Serializer;

trait ExtApproval
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbApproved')]
    protected ?bool $approved = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbExternRefNumber')]
    protected ?string $externRefNumber = null;

    public function getApproved(): ?bool
    {
        return $this->approved;
    }

    public function setApproved(?bool $approved): self
    {
        $this->approved = $approved;
        return $this;
    }

    public function getExternRefNumber(): ?string
    {
        return $this->externRefNumber;
    }

    public function setExternRefNumber(?string $externRefNumber): self
    {
        $this->externRefNumber = $externRefNumber;
        return $this;
    }
}
