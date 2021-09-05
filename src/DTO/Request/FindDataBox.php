<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\OwnerInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\IRequest;

/**
 * Class FindDataBox
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:FindDataBox",namespace="http://isds.czechpoint.cz/v20")
 */
class FindDataBox implements IRequest
{
    /**
     * @Serializer\Type("TomasKulhanek\CzechDataBox\DTO\OwnerInfo")
     * @Serializer\SerializedName("p:dbOwnerInfo")
     * @Serializer\XmlElement(cdata=false)
     */
    protected OwnerInfo $ownerInfo;

    public function getOwnerInfo(): OwnerInfo
    {
        return $this->ownerInfo;
    }

    public function setOwnerInfo(OwnerInfo $ownerInfo): FindDataBox
    {
        $this->ownerInfo = $ownerInfo;
        return $this;
    }
}
