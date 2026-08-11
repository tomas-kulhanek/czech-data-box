<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'AuthenticateMessageResponse', namespace: 'http://isds.czechpoint.cz/v20')]
class AuthenticateMessage extends Response
{
    use DataMessageStatus;

    #[Serializer\Type('bool')]
    #[Serializer\SerializedName(name: 'dmAuthResult')]
    protected bool $authenticated = false;

    public function isAuthenticated(): bool
    {
        return $this->authenticated;
    }
}
