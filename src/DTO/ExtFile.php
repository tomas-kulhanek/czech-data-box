<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Odkaz na dříve nahranou přílohu velkoobjemové datové zprávy (VoDZ)
 * — element dmExtFile v dmFiles operace CreateBigMessage.
 */
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'dmExtFile')]
#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
class ExtFile
{
    #[Serializer\Type('string')]
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('dmFileMetaType')]
    #[Assert\NotBlank(allowNull: false)]
    protected string $metaType;

    #[Serializer\Type('string')]
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('dmAttID')]
    #[Assert\NotBlank(allowNull: false)]
    protected string $attachmentId;

    #[Serializer\Type('string')]
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('dmAttHash1')]
    protected string $attachmentHash1;

    #[Serializer\Type('string')]
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('dmAttHash1Alg')]
    protected string $attachmentHash1Algorithm;

    #[Serializer\Type('string')]
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('dmAttHash2')]
    protected string $attachmentHash2;

    #[Serializer\Type('string')]
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('dmAttHash2Alg')]
    protected string $attachmentHash2Algorithm;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('dmFileGuid')]
    protected ?string $guid = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('dmUpFileGuid')]
    protected ?string $upGuid = null;

    public function getMetaType(): string
    {
        return $this->metaType;
    }

    public function setMetaType(string $metaType): ExtFile
    {
        $this->metaType = $metaType;
        return $this;
    }

    public function getAttachmentId(): string
    {
        return $this->attachmentId;
    }

    public function setAttachmentId(string $attachmentId): ExtFile
    {
        $this->attachmentId = $attachmentId;
        return $this;
    }

    public function getAttachmentHash1(): string
    {
        return $this->attachmentHash1;
    }

    public function setAttachmentHash1(string $attachmentHash1): ExtFile
    {
        $this->attachmentHash1 = $attachmentHash1;
        return $this;
    }

    public function getAttachmentHash1Algorithm(): string
    {
        return $this->attachmentHash1Algorithm;
    }

    public function setAttachmentHash1Algorithm(string $attachmentHash1Algorithm): ExtFile
    {
        $this->attachmentHash1Algorithm = $attachmentHash1Algorithm;
        return $this;
    }

    public function getAttachmentHash2(): string
    {
        return $this->attachmentHash2;
    }

    public function setAttachmentHash2(string $attachmentHash2): ExtFile
    {
        $this->attachmentHash2 = $attachmentHash2;
        return $this;
    }

    public function getAttachmentHash2Algorithm(): string
    {
        return $this->attachmentHash2Algorithm;
    }

    public function setAttachmentHash2Algorithm(string $attachmentHash2Algorithm): ExtFile
    {
        $this->attachmentHash2Algorithm = $attachmentHash2Algorithm;
        return $this;
    }

    public function getGuid(): ?string
    {
        return $this->guid;
    }

    public function setGuid(?string $guid): ExtFile
    {
        $this->guid = $guid;
        return $this;
    }

    public function getUpGuid(): ?string
    {
        return $this->upGuid;
    }

    public function setUpGuid(?string $upGuid): ExtFile
    {
        $this->upGuid = $upGuid;
        return $this;
    }
}
