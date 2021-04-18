<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Enum;

use Consistence\Enum\Enum;

class LoginTypeEnum extends Enum
{

    public const LOGIN_NAME_PASSWORD = 'password';
    public const LOGIN_SPIS_CERT = 'cert';
    public const LOGIN_CERT_LOGIN_NAME_PASSWORD = 'certPassword';
    public const LOGIN_HOSTED_SPIS = 'hosted';

}
