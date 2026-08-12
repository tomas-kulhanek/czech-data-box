<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use Generator;
use Symfony\Component\HttpClient\Response\ResponseStream;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

final readonly class StreamingHttpClientDouble implements HttpClientInterface
{
    public function __construct(private StreamingResponseDouble $response)
    {
    }

    /**
     * @param array<string, mixed> $options
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        return $this->response;
    }

    public function stream(ResponseInterface|iterable $responses, ?float $timeout = null): ResponseStreamInterface
    {
        return new ResponseStream($this->createChunks());
    }

    /**
     * @param array<string, mixed> $options
     */
    public function withOptions(array $options): static
    {
        return $this;
    }

    /**
     * @return Generator<StreamingResponseDouble, StreamingChunkDouble>
     */
    private function createChunks(): Generator
    {
        $offset = 0;
        yield $this->response => new StreamingChunkDouble('', true);
        while (($chunk = $this->response->readBody()) !== '') {
            yield $this->response => new StreamingChunkDouble($chunk, false, false, $offset);
            $offset += strlen($chunk);
        }
        yield $this->response => new StreamingChunkDouble('', false, true, $offset);
    }
}
