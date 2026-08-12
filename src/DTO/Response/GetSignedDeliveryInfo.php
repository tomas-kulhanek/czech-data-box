<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'GetSignedDeliveryInfoResponse')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['signature', 'status'])]
class GetSignedDeliveryInfo extends SignedDataMessageResponse
{
}
