<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\Traits\DataMessageEnvelope;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'dmRecord')]
class MessageRecord
{
    use DataMessageEnvelope;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\SerializedName('dmOrdinal')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?int $ordinal = null;

    #[Serializer\Type('int')]
    #[Serializer\SerializedName('dmMessageStatus')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\PositiveOrZero()]
    protected int $messageStatus;

    #[Serializer\Type('int')]
    #[Serializer\SerializedName('dmAttachmentSize')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\Positive()]
    protected int $attachmentSize;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d\\TH:i:s.uP','Europe/Prague'>")]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmDeliveryTime')]
    #[Serializer\SkipWhenEmpty]
    protected ?DateTimeImmutable $deliveryTime = null;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d\\TH:i:s.uP','Europe/Prague'>")]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmAcceptanceTime')]
    #[Serializer\SkipWhenEmpty]
    protected ?DateTimeImmutable $acceptanceTime = null;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmType')]
    #[Serializer\XmlAttribute]
    #[Assert\NotBlank(allowNull: false)]
    protected string $type;

    #[Serializer\Type('bool')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('dmAllowSubstDelivery')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?bool $allowSubstDelivery = null;

    public function getOrdinal(): ?int
    {
        return $this->ordinal;
    }

    public function setOrdinal(?int $ordinal): MessageRecord
    {
        $this->ordinal = $ordinal;
        return $this;
    }

    public function getMessageStatus(): int
    {
        return $this->messageStatus;
    }

    public function setMessageStatus(int $messageStatus): MessageRecord
    {
        $this->messageStatus = $messageStatus;
        return $this;
    }

    public function getAttachmentSize(): int
    {
        return $this->attachmentSize;
    }

    public function setAttachmentSize(int $attachmentSize): MessageRecord
    {
        $this->attachmentSize = $attachmentSize;
        return $this;
    }

    public function getDeliveryTime(): ?DateTimeImmutable
    {
        return $this->deliveryTime;
    }

    public function setDeliveryTime(?DateTimeImmutable $deliveryTime): MessageRecord
    {
        $this->deliveryTime = $deliveryTime;
        return $this;
    }

    public function getAcceptanceTime(): ?DateTimeImmutable
    {
        return $this->acceptanceTime;
    }

    public function setAcceptanceTime(?DateTimeImmutable $acceptanceTime): MessageRecord
    {
        $this->acceptanceTime = $acceptanceTime;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): MessageRecord
    {
        $this->type = $type;
        return $this;
    }

    public function getAllowSubstDelivery(): ?bool
    {
        return $this->allowSubstDelivery;
    }

    public function setAllowSubstDelivery(?bool $allowSubstDelivery): MessageRecord
    {
        $this->allowSubstDelivery = $allowSubstDelivery;
        return $this;
    }

    public function getSenderId(): string
    {
        return $this->senderId;
    }

    public function setSenderId(string $senderID): MessageRecord
    {
        $this->senderId = $senderID;
        return $this;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    public function setSender(string $sender): MessageRecord
    {
        $this->sender = $sender;
        return $this;
    }

    public function getSenderAddress(): ?string
    {
        return $this->senderAddress;
    }

    public function setSenderAddress(?string $senderAddress): MessageRecord
    {
        $this->senderAddress = $senderAddress;
        return $this;
    }

    public function getSenderType(): int
    {
        return $this->senderType;
    }

    public function setSenderType(int $senderType): MessageRecord
    {
        $this->senderType = $senderType;
        return $this;
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function setRecipient(string $recipient): MessageRecord
    {
        $this->recipient = $recipient;
        return $this;
    }

    public function getRecipientAddress(): ?string
    {
        return $this->recipientAddress;
    }

    public function setRecipientAddress(?string $recipientAddress): MessageRecord
    {
        $this->recipientAddress = $recipientAddress;
        return $this;
    }

    public function getAmbiguousRecipient(): ?bool
    {
        return $this->ambiguousRecipient;
    }

    public function setAmbiguousRecipient(?bool $ambiguousRecipient): MessageRecord
    {
        $this->ambiguousRecipient = $ambiguousRecipient;
        return $this;
    }
}
