<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use TomasKulhanek\CzechDataBox\DTO\Hash;
use TomasKulhanek\CzechDataBox\Serializer\SplFileInfo;
use TomasKulhanek\Tests\CzechDataBox\SerializerTrait;

final class SplFileInfoHandlerTest extends TestCase
{
    use SerializerTrait;

    private const string NS = 'http://isds.czechpoint.cz/v20';

    /**
     * @return iterable<string, array{string}>
     */
    public static function contentProvider(): iterable
    {
        yield 'ascii' => ['hello isds'];
        yield 'utf-8' => ['žluťoučký kůň úpěl ďábelské ódy'];
        yield 'binary' => ["\x00\x01\x02\xff\xfe"];
        yield 'single byte' => ['x'];
    }

    #[DataProvider('contentProvider')]
    public function testContentSurvivesSerializationRoundTrip(string $content): void
    {
        $hash = new Hash()
            ->setAlgorithm('SHA-256')
            ->setValue(SplFileInfo::createInTemp($content));

        $xml = self::createSerializer()->serialize($hash, 'xml');
        self::assertStringContainsString(base64_encode($content), $xml);

        $restored = self::deserializeXml($xml, Hash::class);
        self::assertNotNull($restored->getValue());
        self::assertSame($content, $restored->getValue()->getContents());
    }

    public function testEmptyElementDeserializesToNull(): void
    {
        $xml = sprintf('<p:dmHash xmlns:p="%s" algorithm="SHA-256"></p:dmHash>', self::NS);

        self::assertNull(self::deserializeXml($xml, Hash::class)->getValue());
    }

    public function testTemporaryFileIsRemovedWithTheObject(): void
    {
        $file = SplFileInfo::createInTemp('content');
        $path = $file->getPathname();
        self::assertFileExists($path);
        self::assertTrue($file->isTemporary());

        unset($file);

        self::assertFileDoesNotExist($path);
    }

    public function testFileFromDiskIsKept(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'kept-');
        self::assertNotFalse($path);
        file_put_contents($path, 'content');

        $file = new SplFileInfo($path);
        self::assertFalse($file->isTemporary());
        self::assertSame('content', $file->getContents());
        unset($file);

        self::assertFileExists($path);
        unlink($path);
    }

    public function testCreateFromSplFileInfoReadsTheSameContent(): void
    {
        $source = SplFileInfo::createInTemp('shared');
        $copy = SplFileInfo::createFromSplFileInfo($source);

        self::assertSame('shared', $copy->getContents());
        self::assertFalse($copy->isTemporary());
    }

    public function testMissingFileThrowsInsteadOfReturningFalse(): void
    {
        $file = new SplFileInfo('/nonexistent/czech-data-box/attachment.bin');

        $this->expectException(RuntimeException::class);
        $file->getContents();
    }

    public function testToStringReturnsTheContent(): void
    {
        self::assertSame('printed', (string) SplFileInfo::createInTemp('printed'));
    }
}
