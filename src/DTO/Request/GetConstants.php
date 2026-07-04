<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'GetConstants')]
class GetConstants implements IRequest
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\SerializedName('constDate')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?DateTimeImmutable $constDate = null;

    public function getConstDate(): ?DateTimeImmutable
    {
        return $this->constDate;
    }

    public function setConstDate(?DateTimeImmutable $constDate): GetConstants
    {
        $this->constDate = $constDate;
        return $this;
    }
}
