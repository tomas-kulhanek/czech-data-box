<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\Traits\DataMessageId;

#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'dmRecord')]
#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
class StateChangeRecord
{
    use DataMessageId;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d\\TH:i:s.uP','Europe/Prague'>")]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmEventTime')]
    protected DateTimeImmutable $eventTime;

    #[Serializer\Type('int')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmMessageStatus')]
    #[Assert\PositiveOrZero]
    protected int $messageStatus;

    public function getEventTime(): DateTimeImmutable
    {
        return $this->eventTime;
    }

    public function setEventTime(DateTimeImmutable $eventTime): StateChangeRecord
    {
        $this->eventTime = $eventTime;
        return $this;
    }

    public function getMessageStatus(): int
    {
        return $this->messageStatus;
    }

    public function setMessageStatus(int $messageStatus): StateChangeRecord
    {
        $this->messageStatus = $messageStatus;
        return $this;
    }
}
