<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'p:MarkMessageAsDownloadedResponse', namespace: 'http://isds.czechpoint.cz/v20')]
class MarkMessageAsDownloaded extends IResponse
{
	use DataMessageStatus;
}
