<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'RegisterForNotifications')]
class RegisterForNotifications implements IRequest
{
    #[Serializer\Type('int')]
    #[Serializer\SerializedName('action')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected int $action;

    public function getAction(): int
    {
        return $this->action;
    }

    public function setAction(int $action): RegisterForNotifications
    {
        $this->action = $action;
        return $this;
    }
}
