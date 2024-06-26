<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataBoxId;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'CheckDataBox')]
class CheckDataBox implements IRequest
{
    use DataBoxId;

    #[Serializer\Type('booL')]
    #[Serializer\SerializedName('dbApproved')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SkipWhenEmpty]
    protected ?bool $approved = null;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dbExternRefNumber')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SkipWhenEmpty]
    protected ?string $externalRefNumber = null;

    public function getApproved(): ?bool
    {
        return $this->approved;
    }

    public function setApproved(?bool $approved): CheckDataBox
    {
        $this->approved = $approved;
        return $this;
    }

    public function getExternalRefNumber(): ?string
    {
        return $this->externalRefNumber;
    }

    public function setExternalRefNumber(?string $externalRefNumber): CheckDataBox
    {
        $this->externalRefNumber = $externalRefNumber;
        return $this;
    }
}
