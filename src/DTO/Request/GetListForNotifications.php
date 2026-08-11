<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'GetListForNotifications')]
class GetListForNotifications implements Request
{
    #[Serializer\Type("DateTimeImmutable<'Y-m-d\\TH:i:s.uP','Europe/Prague'>")]
    #[Serializer\SerializedName('ntfFromTime')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected DateTimeImmutable $fromTime;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('ntfScope')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected string $scope;

    public function getFromTime(): DateTimeImmutable
    {
        return $this->fromTime;
    }

    public function setFromTime(DateTimeImmutable $fromTime): GetListForNotifications
    {
        $this->fromTime = $fromTime;
        return $this;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function setScope(string $scope): GetListForNotifications
    {
        $this->scope = $scope;
        return $this;
    }
}
