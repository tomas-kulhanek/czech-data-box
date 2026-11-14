<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Provider;

use Generator;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Throwable;

use function min;
use function strlen;
use function substr;

use const SEEK_CUR;
use const SEEK_END;
use const SEEK_SET;

/**
 * PSR-7 pohled na {@see StreamedRequestBody} pro HTTP klienty postavené na PSR-7 (Guzzle).
 *
 * Velikost je známá předem a stream jde převinout, takže klient může poslat `Content-Length`
 * a při opakování spojení (např. po uzavřeném keep-alive) tělo přehraje znovu.
 */
final class StreamedRequestBodyStream implements StreamInterface
{
    private ?StreamedRequestBody $body;

    /**
     * @var Generator<int, string>|null
     */
    private ?Generator $chunks = null;

    private string $buffer = '';

    private int $position = 0;

    public function __construct(StreamedRequestBody $body)
    {
        $this->body = $body;
    }

    public function __toString(): string
    {
        try {
            $this->rewind();

            return $this->getContents();
        } catch (Throwable) {
            // PSR-7 zakazuje z __toString() vyhazovat výjimky
            return '';
        }
    }

    public function close(): void
    {
        $this->body = null;
        $this->chunks = null;
        $this->buffer = '';
    }

    public function detach()
    {
        $this->close();

        return null;
    }

    public function getSize(): ?int
    {
        return $this->body?->getContentLength();
    }

    public function tell(): int
    {
        return $this->position;
    }

    public function eof(): bool
    {
        $size = $this->getSize();

        return $size === null || $this->position >= $size;
    }

    public function isSeekable(): bool
    {
        return $this->body !== null;
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        $size = $this->getSize();
        if ($size === null) {
            throw new RuntimeException('The stream is closed.');
        }
        $target = match ($whence) {
            SEEK_SET => $offset,
            SEEK_CUR => $this->position + $offset,
            SEEK_END => $size + $offset,
            default => throw new RuntimeException('Invalid seek mode ' . $whence . '.'),
        };
        if ($target < 0 || $target > $size) {
            throw new RuntimeException('Unable to seek to position ' . $target . '.');
        }

        if ($target < $this->position) {
            $this->restart();
        }
        while ($this->position < $target) {
            $skipped = $this->read(min(1024 * 1024, $target - $this->position));
            if ($skipped === '') {
                throw new RuntimeException('Unable to seek to position ' . $target . '.');
            }
        }
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable(): bool
    {
        return false;
    }

    public function write(string $string): int
    {
        throw new RuntimeException('The request body stream is read-only.');
    }

    public function isReadable(): bool
    {
        return $this->body !== null;
    }

    public function read(int $length): string
    {
        if ($this->body === null) {
            throw new RuntimeException('The stream is closed.');
        }
        if ($length < 1) {
            throw new RuntimeException('Length must be greater than zero.');
        }

        $this->chunks ??= $this->body->getIterator();
        while (strlen($this->buffer) < $length && $this->chunks->valid()) {
            $this->buffer .= $this->chunks->current();
            $this->chunks->next();
        }

        $data = substr($this->buffer, 0, $length);
        $this->buffer = substr($this->buffer, strlen($data));
        $this->position += strlen($data);

        return $data;
    }

    public function getContents(): string
    {
        $contents = '';
        while (!$this->eof()) {
            $chunk = $this->read(1024 * 1024);
            if ($chunk === '') {
                break;
            }
            $contents .= $chunk;
        }

        return $contents;
    }

    public function getMetadata(?string $key = null)
    {
        return $key === null ? [] : null;
    }

    private function restart(): void
    {
        $this->chunks = null;
        $this->buffer = '';
        $this->position = 0;
    }
}
