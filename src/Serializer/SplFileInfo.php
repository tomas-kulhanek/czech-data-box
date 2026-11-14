<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Serializer;

use JMS\Serializer\Annotation as Serializer;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use SplFileInfo as BaseSplFileInfo;

use function fclose;
use function feof;
use function file_get_contents;
use function file_put_contents;
use function fopen;
use function fread;
use function fwrite;
use function get_debug_type;
use function is_resource;
use function strlen;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

class SplFileInfo extends BaseSplFileInfo
{
    private const int COPY_CHUNK_SIZE = 1024 * 1024;

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

    /**
     * Přečte stream od jeho aktuální pozice do konce a uloží ho do dočasného souboru.
     *
     * Kopíruje se po blocích, takže paměť nezávisí na velikosti přílohy. Stream zůstává otevřený
     * a jeho pozice po zavolání ukazuje na konec; zavřít ho je na volajícím. Dočasný soubor se
     * smaže s objektem (viz {@see self::isTemporary()}).
     *
     * @param mixed $stream resource streamu (například z `tmpfile()` nebo `fopen()`) nebo PSR-7 `StreamInterface`;
     *        cokoli jiného skončí výjimkou
     * @throws RuntimeException
     */
    public static function createFromStream(mixed $stream): self
    {
        $isResource = is_resource($stream);
        if (!$isResource && !$stream instanceof StreamInterface) {
            throw new RuntimeException(
                'The attachment stream must be a stream resource or a PSR-7 StreamInterface, '
                . get_debug_type($stream) . ' given.'
            );
        }

        $path = tempnam(sys_get_temp_dir(), 'czech-data-box-');
        if ($path === false) {
            throw new RuntimeException('Cannot create a temporary file for the attachment content.');
        }

        $file = new self($path, true);
        $target = @fopen($path, 'wb');
        if ($target === false) {
            throw new RuntimeException('Cannot open ' . $path . ' for writing.');
        }

        try {
            while (!($isResource ? feof($stream) : $stream->eof())) {
                $chunk = $isResource ? fread($stream, self::COPY_CHUNK_SIZE) : $stream->read(self::COPY_CHUNK_SIZE);
                if ($chunk === false) {
                    throw new RuntimeException('Cannot read the attachment stream.');
                }
                if ($chunk === '') {
                    continue;
                }
                if (fwrite($target, $chunk) !== strlen($chunk)) {
                    throw new RuntimeException('Cannot write the attachment content into ' . $path . '.');
                }
            }
        } finally {
            fclose($target);
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
