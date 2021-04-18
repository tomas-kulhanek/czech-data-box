<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Traits;

use JMS\Serializer\Annotation as Serializer;

trait DataMessageEnvelope
{

    use DataMessageEnvelopeSub;
    use DataMessageId;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("p:dbIDSender")
     * @Serializer\XmlElement(cdata=false)
     */
    protected string $senderId;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("p:dmSender")
     * @Serializer\XmlElement(cdata=false)
     */
    protected string $sender;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("p:dmSenderAddress")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SkipWhenEmpty
     */
    protected ?string $senderAddress = null;

    /**
     * @Serializer\Type("int")
     * @Serializer\SerializedName("p:dmSenderType")
     * @Serializer\XmlElement(cdata=false)
     */
    protected int $senderType;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("p:dmRecipient")
     * @Serializer\XmlElement(cdata=false)
     */
    protected string $recipient;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("p:dmRecipientAddress")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SkipWhenEmpty
     */
    protected ?string $recipientAddress = null;

    /**
     * @Serializer\SkipWhenEmpty()
     * @Serializer\Type("bool")
     * @Serializer\SerializedName("p:dmAmbiguousRecipient")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?bool $ambiguousRecipient = null;

    public function getSenderId(): string
    {
        return $this->senderId;
    }

    public function setSenderId(string $senderId): self
    {
        $this->senderId = $senderId;
        return $this;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    public function setSender(string $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function getSenderAddress(): ?string
    {
        return $this->senderAddress;
    }

    public function setSenderAddress(?string $senderAddress): self
    {
        $this->senderAddress = $senderAddress;
        return $this;
    }

    public function getSenderType(): int
    {
        return $this->senderType;
    }

    public function setSenderType(int $senderType): self
    {
        $this->senderType = $senderType;
        return $this;
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function setRecipient(string $recipient): self
    {
        $this->recipient = $recipient;
        return $this;
    }

    public function getRecipientAddress(): ?string
    {
        return $this->recipientAddress;
    }

    public function setRecipientAddress(?string $recipientAddress): self
    {
        $this->recipientAddress = $recipientAddress;
        return $this;
    }

    public function getAmbiguousRecipient(): ?bool
    {
        return $this->ambiguousRecipient;
    }

    public function setAmbiguousRecipient(?bool $ambiguousRecipient): self
    {
        $this->ambiguousRecipient = $ambiguousRecipient;
        return $this;
    }

}
