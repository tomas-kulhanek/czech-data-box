<?php

namespace TomasKulhanek\CzechDataBox\Enum;

enum LoginTypeEnum: string
{
    case NAME_PASSWORD = 'password';
    case SPIS_CERT = 'cert';
    case CERT_LOGIN_NAME_PASSWORD = 'certPassword';
    case HOSTED_SPIS = 'hosted';
}
