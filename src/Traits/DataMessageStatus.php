<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Traits;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\Response\IResponseStatus;

trait DataMessageStatus
{
    /**
     * @Serializer\Type("TomasKulhanek\CzechDataBox\DTO\DataMessageStatus")
     * @Serializer\SerializedName("p:dmStatus")
     * @Serializer\XmlElement(cdata=false)
     */
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
