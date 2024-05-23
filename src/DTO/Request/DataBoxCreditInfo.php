<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataBoxId;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'DataBoxCreditInfo')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['dataBoxId', 'fromDate', 'toDate'])]
class DataBoxCreditInfo implements IRequest
{
    use DataBoxId;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('ciFromDate')]
    private DateTimeImmutable $fromDate;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
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
