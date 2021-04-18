<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Request;

use TomasKulhanek\CzechDataBox\IRequest;
use TomasKulhanek\CzechDataBox\Traits\DataMessageId;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class GetDeliveryInfo
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:GetDeliveryInfo",namespace="http://isds.czechpoint.cz/v20")
 */
class GetDeliveryInfo implements IRequest
{

    use DataMessageId;
}
