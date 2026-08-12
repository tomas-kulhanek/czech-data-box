<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\ReturnedMessageEnvelope;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'SentMessageEnvelopeDownloadResponse')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['message', 'status'])]
class SentMessageEnvelopeDownload extends DataMessageResponse
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type(ReturnedMessageEnvelope::class)]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmReturnedMessageEnvelope')]
    #[Assert\Valid()]
    protected ?ReturnedMessageEnvelope $message = null;

    public function getMessage(): ?ReturnedMessageEnvelope
    {
        return $this->message;
    }

    public function setMessage(?ReturnedMessageEnvelope $message): SentMessageEnvelopeDownload
    {
        $this->message = $message;
        return $this;
    }
}
