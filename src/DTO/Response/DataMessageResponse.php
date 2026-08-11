<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\DataMessageStatus;

abstract class DataMessageResponse extends Response
{
    #[Serializer\Type(DataMessageStatus::class)]
    #[Serializer\SerializedName('dmStatus')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Assert\Valid()]
    protected ResponseStatus $status;

    public function getStatus(): ResponseStatus
    {
        return $this->status;
    }

    public function setStatus(ResponseStatus $status): static
    {
        $this->status = $status;
        return $this;
    }
}
