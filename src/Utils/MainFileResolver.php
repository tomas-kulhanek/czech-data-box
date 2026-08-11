<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Utils;

use TomasKulhanek\CzechDataBox\DTO\File;

final class MainFileResolver
{
    private function __construct()
    {
    }

    /**
     * @param File[] $files
     */
    public static function resolve(array $files): ?File
    {
        foreach ($files as $file) {
            if ($file->getMetaType() === 'main') {
                return $file;
            }
        }

        return null;
    }
}
