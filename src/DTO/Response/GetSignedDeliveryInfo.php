<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;
use TomasKulhanek\CzechDataBox\Traits\Signature;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'GetSignedDeliveryInfoResponse')]
class GetSignedDeliveryInfo extends IResponse
{
    use DataMessageStatus;
    use Signature;
}
