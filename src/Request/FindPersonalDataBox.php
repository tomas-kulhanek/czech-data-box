<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Request;

use TomasKulhanek\CzechDataBox\Entity\PersonalOwnerInfo;
use TomasKulhanek\CzechDataBox\IRequest;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class FindPersonalDataBox
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:FindPersonalDataBox",namespace="http://isds.czechpoint.cz/v20")
 */
class FindPersonalDataBox implements IRequest
{

    /**
     * @Serializer\Type("TomasKulhanek\CzechDataBox\Entity\PersonalOwnerInfo")
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
