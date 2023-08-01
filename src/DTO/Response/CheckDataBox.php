<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'p:FindDataBoxResponse', namespace: 'http://isds.czechpoint.cz/v20')]
class CheckDataBox extends IResponse
{
	use DataBoxStatus;

	#[Serializer\SkipWhenEmpty]
	#[Serializer\Type('int')]
	#[Serializer\SerializedName('p:dbState')]
	#[Serializer\XmlElement(cdata: false)]
	protected ?int $state = null;

	public function getState(): ?int
	{
		return $this->state;
	}

	public function setState(?int $state): CheckDataBox
	{
		$this->state = $state;
		return $this;
	}
}
