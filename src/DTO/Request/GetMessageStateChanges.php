<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'p:GetMessageStateChanges', namespace: 'http://isds.czechpoint.cz/v20')]
class GetMessageStateChanges implements IRequest
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d\\TH:i:s.uP','Europe/Prague'>")]
    #[Serializer\SerializedName('p:dmFromTime')]
    #[Serializer\XmlElement(cdata: false)]
    protected ?DateTimeImmutable $changesFrom = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d\\TH:i:s.uP','Europe/Prague'>")]
    #[Serializer\SerializedName('p:dmToTime')]
    #[Serializer\XmlElement(cdata: false)]
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
