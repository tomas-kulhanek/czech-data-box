<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\Utils\BinarySuffix;

class BinarySuffixTest extends TestCase
{
    /**
     * @return iterable<string, array{int, string}>
     */
    public static function sizeProvider(): iterable
    {
        yield 'bytes' => [512, '512 bytes'];
        yield 'kilobytes' => [2048, '2.0 kB'];
        yield 'exact limit' => [20 * 1024 ** 2, '20.0 MB'];
        yield 'just below the limit' => [20 * 1024 ** 2 - 1, '20.0 MB'];
        yield 'fraction is not truncated' => [21_949_235, '20.9 MB'];
        yield 'gigabytes' => [3 * 1024 ** 3, '3.0 GB'];
        yield 'negative' => [-1, '-1'];
    }

    #[DataProvider('sizeProvider')]
    public function testConvert(int $size, string $expected): void
    {
        self::assertSame($expected, BinarySuffix::convert($size));
    }

    public function testSizesJustOverTheLimitAreDistinguishable(): void
    {
        self::assertNotSame(
            BinarySuffix::convert(20 * 1024 ** 2),
            BinarySuffix::convert(21 * 1024 ** 2)
        );
    }
}
