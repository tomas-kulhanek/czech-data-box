<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'GetMessageStateChanges')]
class GetMessageStateChanges implements IRequest
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d\\TH:i:s.uP','Europe/Prague'>")]
    #[Serializer\SerializedName('dmFromTime')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?DateTimeImmutable $changesFrom = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d\\TH:i:s.uP','Europe/Prague'>")]
    #[Serializer\SerializedName('dmToTime')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?DateTimeImmutable $changesTo = null;

    public function getChangesFrom(): ?DateTimeImmutable
    {
        return $this->changesFrom;
    }

    public function setChangesFrom(?DateTimeImmutable $changesFrom): GetMessageStateChanges
    {
        $this->changesFrom = $changesFrom;
        return $this;
    }

    public function getChangesTo(): ?DateTimeImmutable
    {
        return $this->changesTo;
    }

    public function setChangesTo(?DateTimeImmutable $changesTo): GetMessageStateChanges
    {
        $this->changesTo = $changesTo;
        return $this;
    }
}
