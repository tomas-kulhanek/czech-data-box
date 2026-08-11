<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataBoxId;
use TomasKulhanek\CzechDataBox\Traits\ExtApproval;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'SetOpenAddressing')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['dataBoxId', 'approved', 'externRefNumber'])]
class SetOpenAddressing implements Request
{
    use DataBoxId;
    use ExtApproval;
}
