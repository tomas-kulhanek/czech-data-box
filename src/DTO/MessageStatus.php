<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\Response\IResponseStatus;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'p:dmSingleStatus')]
class MessageStatus
{
    #[Serializer\Type(DataMessageStatus::class)]
    #[Serializer\SerializedName('dmStatus')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Assert\Valid()]
    protected IResponseStatus $status;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmID')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $dataMessageId = null;

    public function getStatus(): IResponseStatus
    {
        return $this->status;
    }

    public function setStatus(DataMessageStatus $status): MessageStatus
    {
        $this->status = $status;
        return $this;
    }

    public function getDataMessageId(): ?string
    {
        return $this->dataMessageId;
    }

    public function setDataMessageId(?string $dataMessageId): MessageStatus
    {
        $this->dataMessageId = $dataMessageId;
        return $this;
    }
}
