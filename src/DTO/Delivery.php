<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\Traits\QTimestamp;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'p:dmDelivery')]
class Delivery
{
    use QTimestamp;

    #[Serializer\Type(MessageEnvelope::class)]
    #[Serializer\SerializedName('p:dmDm')]
    #[Serializer\XmlElement(cdata: false)]
    #[Assert\Valid()]
    protected MessageEnvelope $dataMessage;

    #[Serializer\Type(Hash::class)]
    #[Serializer\SerializedName('p:dmHash')]
    #[Serializer\XmlElement(cdata: false)]
    #[Assert\Valid()]
    protected Hash $hash;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d\\TH:i:s.uP','Europe/Prague'>")]
    #[Serializer\XmlElement(cdata: false)]
    #[Serializer\SerializedName('p:dmDeliveryTime')]
    #[Serializer\SkipWhenEmpty]
    protected ?DateTimeImmutable $deliveryTime = null;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d\\TH:i:s.uP','Europe/Prague'>")]
    #[Serializer\XmlElement(cdata: false)]
    #[Serializer\SerializedName('p:dmAcceptanceTime')]
    #[Serializer\SkipWhenEmpty]
    protected ?DateTimeImmutable $acceptanceTime = null;

    #[Serializer\Type('int')]
    #[Serializer\SerializedName('p:dmMessageStatus')]
    #[Serializer\XmlElement(cdata: false)]
    #[Assert\PositiveOrZero]
    protected int $messageStatus;

    /**
     * @var DataMessageEvent[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\DataMessageEvent>')]
    #[Serializer\XmlList(entry: 'dmEvent', inline: false)]
    #[Serializer\SerializedName('p:dmEvents')]
    #[Assert\All([
        new Assert\Type(type: DataMessageEvent::class)
    ])]
    #[Assert\All()]
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
