<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use PHPUnit\Framework\Assert;
use Psr\Http\Message\StreamInterface;

/**
 * Přečte tělo požadavku tak, jak ho čte HTTP klient: po blocích, bez držení celého těla v paměti.
 */
final class RequestBodyDrain
{
    private const int BLOCK_SIZE = 65_536;

    /**
     * @param callable(string): void $onChunk
     */
    public static function psr7(StreamInterface $body, callable $onChunk): void
    {
        while (!$body->eof()) {
            $onChunk($body->read(self::BLOCK_SIZE));
        }
    }

    /**
     * Symfony předává tělo klientovi jako callable, které vrací bloky a na konci prázdný řetězec.
     *
     * @param callable(string): void $onChunk
     */
    public static function symfony(mixed $body, callable $onChunk): void
    {
        Assert::assertIsCallable($body);
        while (true) {
            $chunk = $body(self::BLOCK_SIZE);
            Assert::assertIsString($chunk);
            if ($chunk === '') {
                return;
            }
            $onChunk($chunk);
        }
    }
}
