<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Traits;

use TomasKulhanek\CzechDataBox\IResponseStatus;
use JMS\Serializer\Annotation as Serializer;

trait DataMessageStatus
{

    /**
     * @Serializer\Type("TomasKulhanek\CzechDataBox\Entity\DataMessageStatus")
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
