<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataBoxId;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'p:DataBoxCreditInfo', namespace: 'http://isds.czechpoint.cz/v20')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['dataBoxId', 'fromDate', 'toDate'])]
class DataBoxCreditInfo implements IRequest
{
	use DataBoxId;

	#[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
	#[Serializer\XmlElement(cdata: false)]
	#[Serializer\SerializedName('p:ciFromDate')]
	private DateTimeImmutable $fromDate;

	#[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
	#[Serializer\XmlElement(cdata: false)]
	#[Serializer\SerializedName('p:ciTodate')]
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
