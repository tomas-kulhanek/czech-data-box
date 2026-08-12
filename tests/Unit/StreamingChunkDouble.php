<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use Symfony\Contracts\HttpClient\ChunkInterface;

final readonly class StreamingChunkDouble implements ChunkInterface
{
    public function __construct(
        private string $content = '',
        private bool $first = false,
        private bool $last = false,
        private int $offset = 0
    ) {
    }

    public function isTimeout(): bool
    {
        return false;
    }

    public function isFirst(): bool
    {
        return $this->first;
    }

    public function isLast(): bool
    {
        return $this->last;
    }

    /**
     * @return array{int, array<string, list<string>>}|null
     */
    public function getInformationalStatus(): ?array
    {
        return null;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getError(): ?string
    {
        return null;
    }
}
