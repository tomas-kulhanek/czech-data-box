<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Request;

use TomasKulhanek\CzechDataBox\Entity\OwnerInfo;
use TomasKulhanek\CzechDataBox\IRequest;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class FindDataBox
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:FindDataBox",namespace="http://isds.czechpoint.cz/v20")
 */
class FindDataBox implements IRequest
{

    /**
     * @Serializer\Type("TomasKulhanek\CzechDataBox\Entity\OwnerInfo")
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
