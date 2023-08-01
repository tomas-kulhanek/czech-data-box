<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'p:PDZInfo', namespace: 'http://isds.czechpoint.cz/v20')]
class PDZInfo implements IRequest
{
	#[Serializer\Type('string')]
	#[Serializer\XmlElement(cdata: false)]
	#[Serializer\SerializedName('p:PDZSender')]
	protected string $sender;

	public function getSender(): string
	{
		return $this->sender;
	}

	public function setSender(string $sender): PDZInfo
	{
		$this->sender = $sender;
		return $this;
	}
}
