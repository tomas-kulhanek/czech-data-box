<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\ReturnedMessage;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'p:MessageDownloadResponse', namespace: 'http://isds.czechpoint.cz/v20')]
class MessageDownload extends IResponse
{
    use DataMessageStatus;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type(ReturnedMessage::class)]
    #[Serializer\SerializedName('p:dmReturnedMessage')]
    #[Serializer\XmlElement(cdata: false)]
    #[Assert\Valid()]
    protected ?ReturnedMessage $returnedMessage = null;

    public function getReturnedMessage(): ?ReturnedMessage
    {
        return $this->returnedMessage;
    }

    public function setReturnedMessage(?ReturnedMessage $returnedMessage): MessageDownload
    {
        $this->returnedMessage = $returnedMessage;
        return $this;
    }
}
