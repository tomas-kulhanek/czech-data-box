<?php

namespace TomasKulhanek\CzechDataBox\Enum;

enum ServiceTypeEnum: string
{
    case OPERATIONS = 'operations';
    case INFO = 'info';
    case SEARCH = 'search';
    case ACCESS = 'access';
    case VODZ = 'vodz';
    case ARCHIVE = 'archive';

    public function usesSoap12(): bool
    {
        return match ($this) {
            self::VODZ, self::ARCHIVE => true,
            default => false,
        };
    }
}
