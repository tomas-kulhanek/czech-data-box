<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\Response\IResponse;
use TomasKulhanek\CzechDataBox\DTO\ReturnedMessageEnvelope;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

/**
 * Class MessageEnvelopeDownload
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:MessageEnvelopeDownloadResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class MessageEnvelopeDownload extends IResponse
{
    use DataMessageStatus;

    /**
     * @Serializer\SkipWhenEmpty()
     * @Serializer\Type("TomasKulhanek\CzechDataBox\DTO\ReturnedMessageEnvelope")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dmReturnedMessageEnvelope")
     */
    protected ?ReturnedMessageEnvelope $message;

    public function setStatus(\TomasKulhanek\CzechDataBox\DTO\DataMessageStatus $status): MessageEnvelopeDownload
    {
        $this->status = $status;
        return $this;
    }

    public function getMessage(): ?ReturnedMessageEnvelope
    {
        return $this->message;
    }

    public function setMessage(?ReturnedMessageEnvelope $message): MessageEnvelopeDownload
    {
        $this->message = $message;
        return $this;
    }
}
