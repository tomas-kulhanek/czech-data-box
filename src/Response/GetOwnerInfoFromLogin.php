<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Response;

use TomasKulhanek\CzechDataBox\Entity\OwnerInfo;
use TomasKulhanek\CzechDataBox\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class GetOwnerInfoFromLogin
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:GetOwnerInfoFromLoginResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class GetOwnerInfoFromLogin extends IResponse
{

    use DataBoxStatus;

    /**
     * @Serializer\Type("TomasKulhanek\CzechDataBox\Entity\OwnerInfo")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dbOwnerInfo")
     */
    protected OwnerInfo $ownerInfo;

    public function getOwnerInfo(): OwnerInfo
    {
        return $this->ownerInfo;
    }

}
