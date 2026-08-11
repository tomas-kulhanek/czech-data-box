<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\AttachmentHash;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'UploadAttachmentResponse')]
class UploadAttachment extends Response
{
    use DataMessageStatus;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmAttID')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $attachmentId = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type(AttachmentHash::class)]
    #[Serializer\SerializedName('dmAttHash1')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Assert\Valid()]
    protected ?AttachmentHash $attachmentHash1 = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type(AttachmentHash::class)]
    #[Serializer\SerializedName('dmAttHash2')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Assert\Valid()]
    protected ?AttachmentHash $attachmentHash2 = null;

    public function getAttachmentId(): ?string
    {
        return $this->attachmentId;
    }

    public function setAttachmentId(?string $attachmentId): UploadAttachment
    {
        $this->attachmentId = $attachmentId;
        return $this;
    }

    public function getAttachmentHash1(): ?AttachmentHash
    {
        return $this->attachmentHash1;
    }

    public function setAttachmentHash1(?AttachmentHash $attachmentHash1): UploadAttachment
    {
        $this->attachmentHash1 = $attachmentHash1;
        return $this;
    }

    public function getAttachmentHash2(): ?AttachmentHash
    {
        return $this->attachmentHash2;
    }

    public function setAttachmentHash2(?AttachmentHash $attachmentHash2): UploadAttachment
    {
        $this->attachmentHash2 = $attachmentHash2;
        return $this;
    }
}
