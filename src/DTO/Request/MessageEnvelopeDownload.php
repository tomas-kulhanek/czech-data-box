<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\Request\IRequest;
use TomasKulhanek\CzechDataBox\Traits\DataMessageId;

/**
 * Class MessageEnvelopeDownload
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:MessageEnvelopeDownload",namespace="http://isds.czechpoint.cz/v20")
 */
class MessageEnvelopeDownload implements IRequest
{
    use DataMessageId;
}
