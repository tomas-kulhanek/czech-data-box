<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\Serializer\Utils\SplFileInfo;

/**
 * Příloha velkoobjemové datové zprávy (VoDZ) nahrávaná operací UploadAttachment.
 */
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'dmFile')]
#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
class BigAttachment
{
    #[Serializer\Type('string')]
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('dmMimeType')]
    #[Assert\NotBlank(allowNull: false)]
    protected string $mimeType;

    #[Serializer\Type('string')]
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('dmFileDescr')]
    #[Assert\NotBlank(allowNull: false)]
    protected string $description;

    #[Serializer\Type('base64File')]
    #[Serializer\SerializedName('dmEncodedContent')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SkipWhenEmpty]
    protected ?SplFileInfo $encodedContent = null;

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): BigAttachment
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): BigAttachment
    {
        $this->description = $description;
        return $this;
    }

    public function getEncodedContent(): ?SplFileInfo
    {
        return $this->encodedContent;
    }

    public function setEncodedContent(?SplFileInfo $encodedContent): BigAttachment
    {
        $this->encodedContent = $encodedContent;
        return $this;
    }
}
