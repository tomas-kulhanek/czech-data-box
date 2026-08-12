<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Serializer\SplFileInfo;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'Re-signISDSDocumentResponse')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['document', 'validTo', 'status'])]
class ResignISDSDocument extends DataMessageResponse
{
    #[Serializer\Type('base64File')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmResultDoc')]
    protected ?SplFileInfo $document = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\SerializedName('dmValidTo')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?DateTimeImmutable $validTo = null;

    public function getDocument(): ?SplFileInfo
    {
        return $this->document;
    }

    public function setDocument(?SplFileInfo $document): ResignISDSDocument
    {
        $this->document = $document;
        return $this;
    }

    public function getValidTo(): ?DateTimeImmutable
    {
        return $this->validTo;
    }

    public function setValidTo(?DateTimeImmutable $validTo): ResignISDSDocument
    {
        $this->validTo = $validTo;
        return $this;
    }
}
