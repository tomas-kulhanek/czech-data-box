<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'AddDataBoxUser2Response')]
class AddDataBoxUser2 extends Response
{
    use DataBoxStatus;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dbID')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $dataBoxId = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dbAccessDataId')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $accessDataId = null;

    public function getDataBoxId(): ?string
    {
        return $this->dataBoxId;
    }

    public function getAccessDataId(): ?string
    {
        return $this->accessDataId;
    }
}
