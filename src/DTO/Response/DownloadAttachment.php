<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\BigAttachmentDownload;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'DownloadAttachmentResponse')]
class DownloadAttachment extends Response
{
    use DataMessageStatus;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type(BigAttachmentDownload::class)]
    #[Serializer\SerializedName('dmFile')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Assert\Valid()]
    protected ?BigAttachmentDownload $file = null;

    public function getFile(): ?BigAttachmentDownload
    {
        return $this->file;
    }

    public function setFile(?BigAttachmentDownload $file): DownloadAttachment
    {
        $this->file = $file;
        return $this;
    }
}
