<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\DataBoxStatus;

abstract class DataBoxResponse extends Response
{
    #[Serializer\Type(DataBoxStatus::class)]
    #[Serializer\SerializedName('dbStatus')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
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
