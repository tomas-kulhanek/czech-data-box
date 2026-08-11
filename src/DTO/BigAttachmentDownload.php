<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\Serializer\Utils\SplFileInfo;

/**
 * Příloha velkoobjemové datové zprávy (VoDZ) vracená operací DownloadAttachment.
 */
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'dmFile')]
#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
class BigAttachmentDownload
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('dmFileMetaType')]
    protected ?string $metaType = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('dmMimeType')]
    protected ?string $mimeType = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('dmFileDescr')]
    protected ?string $description = null;

    #[Serializer\Type('base64File')]
    #[Serializer\SerializedName('dmEncodedContent')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SkipWhenEmpty]
    protected ?SplFileInfo $encodedContent = null;

    public function getMetaType(): ?string
    {
        return $this->metaType;
    }

    public function setMetaType(?string $metaType): BigAttachmentDownload
    {
        $this->metaType = $metaType;
        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): BigAttachmentDownload
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): BigAttachmentDownload
    {
        $this->description = $description;
        return $this;
    }

    public function getEncodedContent(): ?SplFileInfo
    {
        return $this->encodedContent;
    }

    public function setEncodedContent(?SplFileInfo $encodedContent): BigAttachmentDownload
    {
        $this->encodedContent = $encodedContent;
        return $this;
    }
}
