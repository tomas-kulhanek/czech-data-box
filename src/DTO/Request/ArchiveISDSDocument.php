<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Serializer\SplFileInfo;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'ArchiveISDSDocument')]
class ArchiveISDSDocument implements Request
{
    #[Serializer\Type('base64File')]
    #[Serializer\SerializedName('dmMessage')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SkipWhenEmpty]
    protected ?SplFileInfo $dataMessage = null;

    public function getDataMessage(): ?SplFileInfo
    {
        return $this->dataMessage;
    }

    public function setDataMessage(?SplFileInfo $dataMessage): ArchiveISDSDocument
    {
        $this->dataMessage = $dataMessage;
        return $this;
    }
}
