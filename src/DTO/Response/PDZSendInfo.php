<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'p:PDZSendInfoResponse', namespace: 'http://isds.czechpoint.cz/v20')]
class PDZSendInfo extends IResponse
{
    use DataBoxStatus;

    #[Serializer\Type('bool')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('PDZsiResult')]
    protected bool $result;

    public function isResult(): bool
    {
        return $this->result;
    }
}
