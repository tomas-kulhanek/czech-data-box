<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Traits;

use JMS\Serializer\Annotation as Serializer;

trait Address
{
	#[Serializer\SkipWhenEmpty]
	#[Serializer\Type('string')]
	#[Serializer\XmlElement(cdata: false)]
	#[Serializer\SerializedName('p:adCity')]
	protected ?string $adCity = null;

	#[Serializer\SkipWhenEmpty]
	#[Serializer\Type('string')]
	#[Serializer\XmlElement(cdata: false)]
	#[Serializer\SerializedName('p:adStreet')]
	protected ?string $adStreet = null;

	#[Serializer\SkipWhenEmpty]
	#[Serializer\Type('string')]
	#[Serializer\XmlElement(cdata: false)]
	#[Serializer\SerializedName('p:adNumberInStreet')]
	protected ?string $adNumberInStreet = null;

	#[Serializer\SkipWhenEmpty]
	#[Serializer\Type('string')]
	#[Serializer\XmlElement(cdata: false)]
	#[Serializer\SerializedName('p:adNumberInMunicipality')]
	protected ?string $adNumberInMunicipality = null;

	#[Serializer\SkipWhenEmpty]
	#[Serializer\Type('string')]
	#[Serializer\XmlElement(cdata: false)]
	#[Serializer\SerializedName('p:adZipCode')]
	protected ?string $adZipCode = null;

	#[Serializer\SkipWhenEmpty]
	#[Serializer\Type('string')]
	#[Serializer\XmlElement(cdata: false)]
	#[Serializer\SerializedName('p:adState')]
	protected ?string $adState = null;

	public function getAdCity(): ?string
	{
		return $this->adCity;
	}

	public function setAdCity(?string $adCity): self
	{
		$this->adCity = $adCity;
		return $this;
	}

	public function getAdStreet(): ?string
	{
		return $this->adStreet;
	}

	public function setAdStreet(?string $adStreet): self
	{
		$this->adStreet = $adStreet;
		return $this;
	}

	public function getAdNumberInStreet(): ?string
	{
		return $this->adNumberInStreet;
	}

	public function setAdNumberInStreet(?string $adNumberInStreet): self
	{
		$this->adNumberInStreet = $adNumberInStreet;
		return $this;
	}

	public function getAdNumberInMunicipality(): ?string
	{
		return $this->adNumberInMunicipality;
	}

	public function setAdNumberInMunicipality(?string $adNumberInMunicipality): self
	{
		$this->adNumberInMunicipality = $adNumberInMunicipality;
		return $this;
	}

	public function getAdZipCode(): ?string
	{
		return $this->adZipCode;
	}

	public function setAdZipCode(?string $adZipCode): self
	{
		$this->adZipCode = $adZipCode;
		return $this;
	}

	public function getAdState(): ?string
	{
		return $this->adState;
	}

	public function setAdState(?string $adState): self
	{
		$this->adState = $adState;
		return $this;
	}
}
