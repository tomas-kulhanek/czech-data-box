<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use GuzzleHttp\Psr7\PumpStream;
use Psr\Http\Message\StreamInterface;

final class GeneratedResponseBody
{
    public int $servedBytes = 0;

    private int $remainingBytes;

    public function __construct(private readonly int $size, private readonly int $chunkSize)
    {
        $this->remainingBytes = $size;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function read(): string
    {
        if ($this->remainingBytes <= 0) {
            return '';
        }
        $length = min($this->chunkSize, $this->remainingBytes);
        $this->remainingBytes -= $length;
        $this->servedBytes += $length;

        return str_repeat('a', $length);
    }

    public function toStream(): StreamInterface
    {
        return new PumpStream(function (): string|false {
            $chunk = $this->read();

            return $chunk === '' ? false : $chunk;
        });
    }
}
