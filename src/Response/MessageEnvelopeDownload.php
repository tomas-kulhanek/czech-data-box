<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Response;

use TomasKulhanek\CzechDataBox\Entity\ReturnedMessageEnvelope;
use TomasKulhanek\CzechDataBox\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;
use JMS\Serializer\Annotation as Serializer;

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
     * @Serializer\Type("TomasKulhanek\CzechDataBox\Entity\ReturnedMessageEnvelope")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dmReturnedMessageEnvelope")
     */
    protected ?ReturnedMessageEnvelope $message;

    public function setStatus(\TomasKulhanek\CzechDataBox\Entity\DataMessageStatus $status): MessageEnvelopeDownload
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
