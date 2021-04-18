<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Traits;

use TomasKulhanek\CzechDataBox\IResponseStatus;
use JMS\Serializer\Annotation as Serializer;

trait DataBoxStatus
{

    /**
     * @Serializer\Type("TomasKulhanek\CzechDataBox\Entity\DataBoxStatus")
     * @Serializer\SerializedName("p:dbStatus")
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
