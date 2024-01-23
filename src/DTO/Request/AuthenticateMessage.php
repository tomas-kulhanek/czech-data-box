<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\Serializer\Utils\SplFileInfo;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'p:AuthenticateMessage', namespace: 'http://isds.czechpoint.cz/v20')]
#[Serializer\AccessType(type: 'public_method')]
class AuthenticateMessage implements IRequest
{
    #[Serializer\Type('base64File')]
    #[Serializer\SerializedName('p:dmMessage')]
    #[Serializer\XmlElement(cdata: false)]
    protected SplFileInfo $dataMessage;

    public function getDataMessage(): SplFileInfo
    {
        return $this->dataMessage;
    }

    public function setDataMessage(SplFileInfo $dataMessage): AuthenticateMessage
    {
        $this->dataMessage = $dataMessage;
        return $this;
    }
}
