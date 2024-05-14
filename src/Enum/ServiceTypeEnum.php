<?php

namespace TomasKulhanek\CzechDataBox\Enum;

enum ServiceTypeEnum: string
{
    case OPERATIONS = 'operations';
    case INFO = 'info';
    case SEARCH = 'search';
    case SUPPLEMENTARY = 'supplementary';
    case ACCESS = 'access';
}
