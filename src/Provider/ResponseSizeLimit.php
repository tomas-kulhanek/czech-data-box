<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Provider;

use TomasKulhanek\CzechDataBox\Exception\ConnectionException;
use TomasKulhanek\CzechDataBox\Utils\BinarySuffix;

final readonly class ResponseSizeLimit
{
    public const int DEFAULT_MAX_RESPONSE_SIZE = 256 * 1024 ** 2;

    public const int DEFAULT_CHUNK_SIZE = 256 * 1024;

    public function __construct(
        private int $maxBytes = self::DEFAULT_MAX_RESPONSE_SIZE,
        private int $chunkSize = self::DEFAULT_CHUNK_SIZE
    ) {
    }

    public function withMaxBytes(?int $maxBytes): self
    {
        if ($maxBytes === null || $maxBytes === $this->maxBytes) {
            return $this;
        }

        return new self($maxBytes, $this->chunkSize);
    }

    public function getChunkSize(): int
    {
        return $this->chunkSize;
    }

    public function rejectAnnouncedSize(?string $contentLength): void
    {
        if ($contentLength === null || !ctype_digit($contentLength)) {
            return;
        }
        if ((int) $contentLength > $this->maxBytes) {
            throw $this->tooLarge();
        }
    }

    /**
     * @param iterable<string> $chunks
     */
    public function collect(iterable $chunks): string
    {
        $body = '';
        $readBytes = 0;
        foreach ($chunks as $chunk) {
            $readBytes += strlen($chunk);
            if ($readBytes > $this->maxBytes) {
                throw $this->tooLarge();
            }
            $body .= $chunk;
        }

        return $body;
    }

    private function tooLarge(): ConnectionException
    {
        return new ConnectionException(
            sprintf('The response is larger than the allowed %s.', BinarySuffix::convert($this->maxBytes))
        );
    }
}
