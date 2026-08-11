<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Serializer\SplFileInfo;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'ArchiveISDSDocumentResponse')]
class ArchiveISDSDocument extends DataMessageResponse
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('base64File')]
    #[Serializer\SerializedName('dmResultDoc')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?SplFileInfo $resultDocument = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\SerializedName('nextStampTo')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?DateTimeImmutable $nextStampTo = null;

    public function getResultDocument(): ?SplFileInfo
    {
        return $this->resultDocument;
    }

    public function setResultDocument(?SplFileInfo $resultDocument): ArchiveISDSDocument
    {
        $this->resultDocument = $resultDocument;
        return $this;
    }

    public function getNextStampTo(): ?DateTimeImmutable
    {
        return $this->nextStampTo;
    }

    public function setNextStampTo(?DateTimeImmutable $nextStampTo): ArchiveISDSDocument
    {
        $this->nextStampTo = $nextStampTo;
        return $this;
    }
}
