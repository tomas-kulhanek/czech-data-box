<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;
use TomasKulhanek\Serializer\Utils\SplFileInfo;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'p:Re-signISDSDocumentResponse', namespace: 'http://isds.czechpoint.cz/v20')]
class ResignISDSDocument extends IResponse
{
    use DataMessageStatus;

    #[Serializer\Type('base64File')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmResultDoc')]
    protected SplFileInfo $document;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\SerializedName('dmValidTo')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?DateTimeImmutable $validTo = null;

    public function setStatus(\TomasKulhanek\CzechDataBox\DTO\DataMessageStatus $status): ResignISDSDocument
    {
        $this->status = $status;
        return $this;
    }

    public function getDocument(): SplFileInfo
    {
        return $this->document;
    }

    public function setDocument(SplFileInfo $document): ResignISDSDocument
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
