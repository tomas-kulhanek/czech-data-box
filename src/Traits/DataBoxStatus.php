<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Traits;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\Response\ResponseStatus;

trait DataBoxStatus
{
    #[Serializer\Type(\TomasKulhanek\CzechDataBox\DTO\DataBoxStatus::class)]
    #[Serializer\SerializedName('dbStatus')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ResponseStatus $status;

    public function getStatus(): ResponseStatus
    {
        return $this->status;
    }

    public function setStatus(ResponseStatus $status): self
    {
        $this->status = $status;
        return $this;
    }
}
