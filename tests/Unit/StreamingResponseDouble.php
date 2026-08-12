<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use LogicException;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class StreamingResponseDouble implements ResponseInterface
{
    public bool $canceled = false;

    /**
     * @param array<string, list<string>> $headers
     */
    public function __construct(
        private readonly GeneratedResponseBody $body,
        private readonly array $headers = [],
        private readonly int $statusCode = 200
    ) {
    }

    public function readBody(): string
    {
        return $this->body->read();
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(bool $throw = true): array
    {
        return $this->headers;
    }

    public function getContent(bool $throw = true): string
    {
        throw new LogicException('The response body must be consumed chunk by chunk.');
    }

    /**
     * @return array<array-key, mixed>
     */
    public function toArray(bool $throw = true): array
    {
        throw new LogicException('The response body must be consumed chunk by chunk.');
    }

    public function cancel(): void
    {
        $this->canceled = true;
    }

    public function getInfo(?string $type = null): mixed
    {
        return null;
    }
}
