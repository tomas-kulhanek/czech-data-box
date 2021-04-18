<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Response;

use TomasKulhanek\CzechDataBox\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class MarkMessageAsDownloaded
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:MarkMessageAsDownloadedResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class MarkMessageAsDownloaded extends IResponse
{

    use DataMessageStatus;
}
