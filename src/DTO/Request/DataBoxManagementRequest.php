<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;

abstract class DataBoxManagementRequest implements Request
{
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbID')]
    protected ?string $dataBoxId = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbApproved')]
    protected ?bool $approved = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbExternRefNumber')]
    protected ?string $externRefNumber = null;

    public function getDataBoxId(): ?string
    {
        return $this->dataBoxId;
    }

    public function setDataBoxId(?string $dataBoxId): static
    {
        $this->dataBoxId = $dataBoxId;
        return $this;
    }

    public function getApproved(): ?bool
    {
        return $this->approved;
    }

    public function setApproved(?bool $approved): static
    {
        $this->approved = $approved;
        return $this;
    }

    public function getExternRefNumber(): ?string
    {
        return $this->externRefNumber;
    }

    public function setExternRefNumber(?string $externRefNumber): static
    {
        $this->externRefNumber = $externRefNumber;
        return $this;
    }
}
