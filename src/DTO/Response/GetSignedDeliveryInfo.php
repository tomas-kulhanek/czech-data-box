<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\Response\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;
use TomasKulhanek\CzechDataBox\Traits\Signature;

/**
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:GetSignedDeliveryInfoResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class GetSignedDeliveryInfo extends IResponse
{
    use DataMessageStatus;
    use Signature;
}
