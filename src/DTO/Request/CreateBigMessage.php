<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\BigMessageEnvelope;
use TomasKulhanek\CzechDataBox\DTO\BigMessageFiles;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'CreateBigMessage')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['envelope', 'files'])]
class CreateBigMessage implements Request
{
    #[Serializer\Type(BigMessageEnvelope::class)]
    #[Serializer\SerializedName('dmEnvelope')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\Valid()]
    protected BigMessageEnvelope $envelope;

    #[Serializer\Type(BigMessageFiles::class)]
    #[Serializer\SerializedName('dmFiles')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\Valid()]
    protected BigMessageFiles $files;

    public function getEnvelope(): BigMessageEnvelope
    {
        return $this->envelope;
    }

    public function setEnvelope(BigMessageEnvelope $envelope): CreateBigMessage
    {
        $this->envelope = $envelope;
        return $this;
    }

    public function getFiles(): BigMessageFiles
    {
        return $this->files;
    }

    public function setFiles(BigMessageFiles $files): CreateBigMessage
    {
        $this->files = $files;
        return $this;
    }
}
