<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'DownloadAttachment')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['dataMessageId', 'attachmentNumber'])]
class DownloadAttachment extends DataMessageRequest
{
    #[Serializer\Type('int')]
    #[Serializer\SerializedName('attNum')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected int $attachmentNumber;

    public function getAttachmentNumber(): int
    {
        return $this->attachmentNumber;
    }

    public function setAttachmentNumber(int $attachmentNumber): DownloadAttachment
    {
        $this->attachmentNumber = $attachmentNumber;
        return $this;
    }
}
