<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\Serializer\Utils\SplFileInfo;

#[Serializer\XmlRoot(name: 'p:dmFile')]
#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\AccessType(type: 'public_method')]
class File
{
    #[Serializer\Type('string')]
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('dmMimeType')]
    #[Assert\NotBlank(allowNull: false)]
    protected string $mimeType;

    #[Serializer\Type('string')]
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('dmFileMetaType')]
    #[Assert\NotBlank(allowNull: false)]
    protected string $metaType;

    #[Serializer\Type('string')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('dmFileGuid')]
    protected ?string $guid = null;

    #[Serializer\Type('string')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('dmUpFileGuid')]
    protected ?string $upGuid = null;

    #[Serializer\Type('string')]
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('dmFileDescr')]
    #[Assert\NotBlank(allowNull: false)]
    protected string $description;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('dmFormat')]
    protected ?string $format = null;

    #[Serializer\Type('base64File')]
    #[Serializer\SerializedName('dmEncodedContent')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SkipWhenEmpty]
    protected ?SplFileInfo $encodedContent = null;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmXMLContent')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SkipWhenEmpty]
    protected ?string $xmlContent = null;

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): File
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    public function getMetaType(): string
    {
        return $this->metaType;
    }

    public function setMetaType(string $metaType): File
    {
        $this->metaType = $metaType;
        return $this;
    }

    public function getGuid(): ?string
    {
        return $this->guid;
    }

    public function setGuid(?string $guid): File
    {
        $this->guid = $guid;
        return $this;
    }

    public function getUpGuid(): ?string
    {
        return $this->upGuid;
    }

    public function setUpGuid(?string $upGuid): File
    {
        $this->upGuid = $upGuid;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): File
    {
        $this->description = $description;
        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): File
    {
        $this->format = $format;
        return $this;
    }

    public function getEncodedContent(): ?SplFileInfo
    {
        return $this->encodedContent;
    }

    public function setEncodedContent(SplFileInfo $encodedContent): File
    {
        $this->encodedContent = $encodedContent;
        return $this;
    }

    public function getXmlContent(): ?string
    {
        return $this->xmlContent;
    }

    public function setXmlContent(string $xmlContent): File
    {
        $this->xmlContent = $xmlContent;
        return $this;
    }
}
