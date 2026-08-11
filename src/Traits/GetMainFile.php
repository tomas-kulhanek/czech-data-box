<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Traits;

use TomasKulhanek\CzechDataBox\DTO\File;

trait GetMainFile
{
    /**
     * @return File[]
     */
    abstract public function getFiles(): array;

    public function getMainFile(): ?File
    {
        foreach ($this->getFiles() as $file) {
            if ($file->getMetaType() === 'main') {
                return $file;
            }
        }
        return null;
    }
}
