<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Traits;

use JMS\Serializer\Annotation as Serializer;

trait DataBoxId
{
    /**
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dbID")
     */
    protected string $dataBoxId;

    public function getDataBoxId(): string
    {
        return $this->dataBoxId;
    }

    public function setDataBoxId(string $dataBoxId): self
    {
        $this->dataBoxId = $dataBoxId;
        return $this;
    }
}
