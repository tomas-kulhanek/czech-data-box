<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'DataBoxCreditInfo')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['dataBoxId', 'fromDate', 'toDate'])]
class DataBoxCreditInfo extends DataBoxRequest
{
    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('ciFromDate')]
    private DateTimeImmutable $fromDate;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('ciTodate')]
    private DateTimeImmutable $toDate;

    public function __construct(DateTimeImmutable $fromDate, DateTimeImmutable $toDate)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function getFromDate(): DateTimeImmutable
    {
        return $this->fromDate;
    }

    public function getToDate(): DateTimeImmutable
    {
        return $this->toDate;
    }
}
