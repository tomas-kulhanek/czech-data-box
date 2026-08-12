<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'PDZSendInfoResponse')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['result', 'status'])]
class PDZSendInfo extends DataBoxResponse
{
    #[Serializer\Type('bool')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('PDZsiResult')]
    protected ?bool $result = null;

    public function isResult(): ?bool
    {
        return $this->result;
    }
}
