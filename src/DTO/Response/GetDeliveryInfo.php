<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\Delivery;
use TomasKulhanek\CzechDataBox\DTO\Response\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

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
     * @Serializer\Type("TomasKulhanek\CzechDataBox\DTO\Delivery")
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
