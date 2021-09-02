<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\PersonalOwnerInfo;
use TomasKulhanek\CzechDataBox\DTO\Request\IRequest;

/**
 * Class FindPersonalDataBox
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:FindPersonalDataBox",namespace="http://isds.czechpoint.cz/v20")
 */
class FindPersonalDataBox implements IRequest
{
    /**
     * @Serializer\Type("TomasKulhanek\CzechDataBox\DTO\PersonalOwnerInfo")
     * @Serializer\SerializedName("p:dbOwnerInfo")
     * @Serializer\XmlElement(cdata=false)
     */
    protected PersonalOwnerInfo $ownerInfo;

    public function getOwnerInfo(): PersonalOwnerInfo
    {
        return $this->ownerInfo;
    }

    public function setOwnerInfo(PersonalOwnerInfo $ownerInfo): FindPersonalDataBox
    {
        $this->ownerInfo = $ownerInfo;
        return $this;
    }
}
