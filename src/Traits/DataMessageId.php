<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Traits;

use JMS\Serializer\Annotation as Serializer;

trait DataMessageId
{
	#[Serializer\SkipWhenEmpty]
	#[Serializer\Type('string')]
	#[Serializer\SerializedName('p:dmID')]
	#[Serializer\XmlElement(cdata: false)]
	protected ?string $dataMessageId = null;

	public function getDataMessageId(): ?string
	{
		return $this->dataMessageId;
	}

	public function setDataMessageId(?string $dataMessageId): self
	{
		$this->dataMessageId = $dataMessageId;
		return $this;
	}
}
