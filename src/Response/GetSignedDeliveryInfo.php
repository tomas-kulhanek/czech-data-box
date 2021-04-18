<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Response;

use TomasKulhanek\CzechDataBox\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;
use TomasKulhanek\CzechDataBox\Traits\Signature;
use JMS\Serializer\Annotation as Serializer;

/**
 * todo order
 * Class GetSignedDeliveryInfo
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:GetSignedDeliveryInfoResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class GetSignedDeliveryInfo extends IResponse
{

    use DataMessageStatus;
    use Signature;
}
