<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Enum;

use Consistence\Enum\Enum;

class ServiceTypeEnum extends Enum
{

    public const OPERATIONS = 0;
    public const INFO = 1;
    public const SEARCH = 2;
    public const SUPPLEMENTARY = 3;
    public const ACCESS = 5;

}
