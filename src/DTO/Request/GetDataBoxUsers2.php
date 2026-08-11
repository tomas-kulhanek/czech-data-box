<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataBoxId;
use TomasKulhanek\CzechDataBox\Traits\ExtApproval;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'GetDataBoxUsers2')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['dataBoxId', 'approved', 'externRefNumber'])]
class GetDataBoxUsers2 implements IRequest
{
    use DataBoxId;
    use ExtApproval;
}
