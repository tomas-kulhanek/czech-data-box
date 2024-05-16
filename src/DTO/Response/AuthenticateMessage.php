<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'p:AuthenticateMessageResponse', namespace: 'http://isds.czechpoint.cz/v20')]
class AuthenticateMessage extends IResponse
{
    use DataMessageStatus;

    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('dmAuthResult')]
    protected bool $authenticated;

    public function isAuthenticated(): bool
    {
        return $this->authenticated;
    }

    public function setAuthenticated(bool $authenticated): AuthenticateMessage
    {
        $this->authenticated = $authenticated;
        return $this;
    }
}
