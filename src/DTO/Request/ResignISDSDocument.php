<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\Serializer\Utils\SplFileInfo;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'p:Re-signISDSDocument', namespace: 'http://isds.czechpoint.cz/v20')]
class ResignISDSDocument implements IRequest
{
	#[Serializer\Type('base64File')]
	#[Serializer\XmlElement(cdata: false)]
	#[Serializer\SerializedName('p:dmDoc')]
	protected SplFileInfo $document;

	public function getDocument(): SplFileInfo
	{
		return $this->document;
	}

	public function setDocument(SplFileInfo $document): ResignISDSDocument
	{
		$this->document = $document;
		return $this;
	}
}
