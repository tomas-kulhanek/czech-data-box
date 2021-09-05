<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\Serializer\Utils\SplFileInfo;

/**
 * Class Hash
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:dmHash")
 */
class Hash
{
    /**
     * @Serializer\Type("base64File")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\XmlValue()
     */
    protected SplFileInfo $value;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("algorithm")
     * @Serializer\XmlAttribute()
     */
    protected string $algorithm;

    public function getValue(): SplFileInfo
    {
        return $this->value;
    }

    public function setValue(SplFileInfo $value): Hash
    {
        $this->value = $value;
        return $this;
    }

    public function getAlgorithm(): string
    {
        return $this->algorithm;
    }

    public function setAlgorithm(string $algorithm): Hash
    {
        $this->algorithm = $algorithm;
        return $this;
    }
}
