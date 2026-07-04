<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;

/**
 * Otisk (hash) nahrané přílohy VoDZ vracený operací UploadAttachment
 * (elementy dmAttHash1 a dmAttHash2 s atributem AttHashAlg).
 */
#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
class AttachmentHash
{
    #[Serializer\Type('string')]
    #[Serializer\XmlValue(cdata: false)]
    protected ?string $value = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('AttHashAlg')]
    #[Serializer\XmlAttribute]
    protected ?string $algorithm = null;

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): AttachmentHash
    {
        $this->value = $value;
        return $this;
    }

    public function getAlgorithm(): ?string
    {
        return $this->algorithm;
    }

    public function setAlgorithm(?string $algorithm): AttachmentHash
    {
        $this->algorithm = $algorithm;
        return $this;
    }
}
