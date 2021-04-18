<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Entity;

use DateTimeImmutable;
use TomasKulhanek\CzechDataBox\Traits\DataMessageId;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class StateChangeRecord
 *
 * @Serializer\XmlRoot(name="p:dmRecord")
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 */
class StateChangeRecord
{

    use DataMessageId;

    /**
     * @Serializer\Type("DateTimeImmutable<'Y-m-d\TH:i:s.uP','Europe/Prague'>")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dmEventTime")
     */
    protected DateTimeImmutable $eventTime;

    /**
     * @Serializer\Type("int")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dmMessageStatus")
     */
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
