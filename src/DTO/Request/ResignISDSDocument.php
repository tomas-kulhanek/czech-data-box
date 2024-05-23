<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\Serializer\Utils\SplFileInfo;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'Re-signISDSDocument')]
class ResignISDSDocument implements IRequest
{
    #[Serializer\Type('base64File')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmDoc')]
    protected SplFileInfo $document;

    public function getDocument(): SplFileInfo
    {
        return $this->document;
    }

    public function setDocument(SplFileInfo $document): ResignISDSDocument
    {
        $this->document = $document;
        return $this;
    }
}
