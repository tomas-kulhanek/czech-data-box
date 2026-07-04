<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataMessageId;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'DownloadAttachment')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['dataMessageId', 'attachmentNumber'])]
class DownloadAttachment implements IRequest
{
    use DataMessageId;

    #[Serializer\Type('int')]
    #[Serializer\SerializedName('attNum')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
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
