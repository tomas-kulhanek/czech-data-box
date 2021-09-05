<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\QTimestamp;

/**
 * Class Delivery
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:dmDelivery")
 */
class Delivery
{
    use QTimestamp;

    /**
     * @Serializer\Type("TomasKulhanek\CzechDataBox\DTO\MessageEnvelope")
     * @Serializer\SerializedName("p:dmDm")
     * @Serializer\XmlElement(cdata=false)
     */
    protected MessageEnvelope $dataMessage;

    /**
     * @Serializer\Type("TomasKulhanek\CzechDataBox\DTO\Hash")
     * @Serializer\SerializedName("p:dmHash")
     * @Serializer\XmlElement(cdata=false)
     */
    protected Hash $hash;

    /**
     * @Serializer\Type("DateTimeImmutable<'Y-m-d\TH:i:s.uP','Europe/Prague'>")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dmDeliveryTime")
     * @Serializer\SkipWhenEmpty
     */
    protected ?DateTimeImmutable $deliveryTime = null;

    /**
     * @Serializer\Type("DateTimeImmutable<'Y-m-d\TH:i:s.uP','Europe/Prague'>")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dmAcceptanceTime")
     * @Serializer\SkipWhenEmpty
     */
    protected ?DateTimeImmutable $acceptanceTime = null;

    /**
     * @Serializer\Type("int")
     * @Serializer\SerializedName("p:dmMessageStatus")
     * @Serializer\XmlElement(cdata=false)
     */
    protected int $messageStatus;

    /**
     * @var DataMessageEvent[]
     * @Serializer\Type("array<TomasKulhanek\CzechDataBox\DTO\DataMessageEvent>")
     * @Serializer\XmlList(entry="dmEvent", inline=false)
     * @Serializer\SerializedName("p:dmEvents")
     */
    protected array $events = [];

    public function getHash(): Hash
    {
        return $this->hash;
    }

    public function getDeliveryTime(): ?DateTimeImmutable
    {
        return $this->deliveryTime;
    }

    public function getAcceptanceTime(): ?DateTimeImmutable
    {
        return $this->acceptanceTime;
    }

    public function getMessageStatus(): int
    {
        return $this->messageStatus;
    }

    /**
     * @return DataMessageEvent[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    public function getDataMessage(): MessageEnvelope
    {
        return $this->dataMessage;
    }
}
