<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\OwnerInfo;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'p:GetOwnerInfoFromLoginResponse', namespace: 'http://isds.czechpoint.cz/v20')]
class GetOwnerInfoFromLogin extends IResponse
{
	use DataBoxStatus;

	#[Serializer\Type(OwnerInfo::class)]
	#[Serializer\XmlElement(cdata: false)]
	#[Serializer\SerializedName('p:dbOwnerInfo')]
	#[Assert\Valid()]
	protected OwnerInfo $ownerInfo;

	public function getOwnerInfo(): OwnerInfo
	{
		return $this->ownerInfo;
	}
}
