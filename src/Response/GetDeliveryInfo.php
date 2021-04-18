<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Response;

use TomasKulhanek\CzechDataBox\Entity\Delivery;
use TomasKulhanek\CzechDataBox\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class GetDeliveryInfo
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:GetDeliveryInfoResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class GetDeliveryInfo extends IResponse
{

    use DataMessageStatus;

    /**
     * @Serializer\Type("TomasKulhanek\CzechDataBox\Entity\Delivery")
     * @Serializer\SerializedName("p:dmDelivery")
     * @Serializer\SkipWhenEmpty()
     * @Serializer\XmlElement(cdata=false)
     */
    protected Delivery $delivery;

    public function getDelivery(): Delivery
    {
        return $this->delivery;
    }

}
