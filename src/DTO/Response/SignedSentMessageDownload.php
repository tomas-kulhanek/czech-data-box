<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;
use TomasKulhanek\CzechDataBox\Traits\Signature;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'SignedSentMessageDownloadResponse')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['signature', 'status'])]
class SignedSentMessageDownload extends Response
{
    use DataMessageStatus;
    use Signature;

    public function setStatus(\TomasKulhanek\CzechDataBox\DTO\DataMessageStatus $status): SignedSentMessageDownload
    {
        $this->status = $status;
        return $this;
    }
}
