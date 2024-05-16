<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Traits;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\Response\IResponseStatus;

trait DataBoxStatus
{
    #[Serializer\Type(\TomasKulhanek\CzechDataBox\DTO\DataBoxStatus::class)]
    #[Serializer\SerializedName('dbStatus')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected IResponseStatus $status;

    public function getStatus(): IResponseStatus
    {
        return $this->status;
    }

    public function setStatus(IResponseStatus $status): self
    {
        $this->status = $status;
        return $this;
    }
}
