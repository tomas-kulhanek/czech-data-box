<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'AuthenticateBigMessageResponse')]
class AuthenticateBigMessage extends Response
{
    use DataMessageStatus;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('dmAuthResult')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?bool $authResult = null;

    public function getAuthResult(): ?bool
    {
        return $this->authResult;
    }

    public function setAuthResult(?bool $authResult): AuthenticateBigMessage
    {
        $this->authResult = $authResult;
        return $this;
    }

    public function isAuthenticated(): bool
    {
        return $this->authResult === true;
    }
}
