<?php

namespace TomasKulhanek\CzechDataBox\Enum;

enum FilterEnum: int
{
    case ALL = -1;
    case SUBMITTED = 1;
    case STAMPED = 2;
    case ANTIVIRUS_FAILED = 3;
    case DELIVERED = 4;
    case DELIVERED_AFTER_TIME = 5;
    case DELIVERED_BY_LOGIN = 6;
    case READ = 7;
    case UNDELIVERED = 8;
    case DELETED = 9;
    case IN_VAULT = 10;
}
