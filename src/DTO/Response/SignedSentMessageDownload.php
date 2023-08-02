<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;
use TomasKulhanek\CzechDataBox\Traits\Signature;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'p:SignedSentMessageDownloadResponse', namespace: 'http://isds.czechpoint.cz/v20')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['signature', 'status'])]
class SignedSentMessageDownload extends IResponse
{
	use DataMessageStatus;
	use Signature;

	public function setStatus(\TomasKulhanek\CzechDataBox\DTO\DataMessageStatus $status): SignedSentMessageDownload
	{
		$this->status = $status;
		return $this;
	}
}
