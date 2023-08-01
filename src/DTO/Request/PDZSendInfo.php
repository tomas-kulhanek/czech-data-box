<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'p:PDZSendInfo', namespace: 'http://isds.czechpoint.cz/v20')]
class PDZSendInfo implements IRequest
{
	#[Serializer\Type('string')]
	#[Serializer\XmlElement(cdata: false)]
	#[Serializer\SerializedName('p:dbId')]
	protected string $dataBoxId;

	#[Serializer\Type('string')]
	#[Serializer\XmlElement(cdata: false)]
	#[Serializer\SerializedName('p:PDZType')]
	#[Assert\NotBlank(allowNull: false)]
	protected string $type = 'Normal';

	public function getDataBoxId(): string
	{
		return $this->dataBoxId;
	}

	public function setDataBoxId(string $dataBoxID): PDZSendInfo
	{
		$this->dataBoxId = $dataBoxID;
		return $this;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function setType(string $type): PDZSendInfo
	{
		$this->type = $type;
		return $this;
	}
}
