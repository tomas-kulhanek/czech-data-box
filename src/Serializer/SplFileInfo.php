<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Serializer;

use JMS\Serializer\Annotation as Serializer;
use RuntimeException;
use SplFileInfo as BaseSplFileInfo;

use function file_get_contents;
use function file_put_contents;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

class SplFileInfo extends BaseSplFileInfo
{
    #[Serializer\Exclude]
    private readonly bool $temporary;

    public function __construct(string $fileName, bool $temporary = false)
    {
        $this->temporary = $temporary;
        parent::__construct($fileName);
    }

    public function isTemporary(): bool
    {
        return $this->temporary;
    }

    public static function createInTemp(?string $content): self
    {
        $path = tempnam(sys_get_temp_dir(), 'czech-data-box-');
        if ($path === false) {
            throw new RuntimeException('Cannot create a temporary file for the attachment content.');
        }

        $file = new self($path, true);
        if ($content !== null && file_put_contents($path, $content) === false) {
            throw new RuntimeException('Cannot write the attachment content into ' . $path . '.');
        }

        return $file;
    }

    public static function createFromSplFileInfo(BaseSplFileInfo $file): self
    {
        $path = $file->getRealPath();
        if ($path === false) {
            throw new RuntimeException('The file ' . $file->getPathname() . ' does not exist.');
        }

        return new self($path);
    }

    /**
     * @throws RuntimeException
     */
    public function getContents(): string
    {
        $content = @file_get_contents($this->getPathname());
        if ($content === false) {
            throw new RuntimeException('Cannot read the contents of ' . $this->getPathname() . '.');
        }

        return $content;
    }

    public function __toString(): string
    {
        return $this->getContents();
    }

    public function __destruct()
    {
        if (!$this->temporary) {
            return;
        }
        $path = $this->getRealPath();
        if ($path !== false) {
            @unlink($path);
        }
    }
}
