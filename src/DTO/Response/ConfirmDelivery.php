<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\Response\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

/**
 * Class ConfirmDelivery
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:ConfirmDeliveryResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class ConfirmDelivery extends IResponse
{
    use DataMessageStatus;
}
