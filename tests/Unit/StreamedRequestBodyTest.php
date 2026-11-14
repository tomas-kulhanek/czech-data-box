<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use JMS\Serializer\SerializationContext;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use TomasKulhanek\CzechDataBox\DTO\BigAttachment;
use TomasKulhanek\CzechDataBox\DTO\Envelope;
use TomasKulhanek\CzechDataBox\DTO\File;
use TomasKulhanek\CzechDataBox\DTO\Recipient;
use TomasKulhanek\CzechDataBox\DTO\Request\CreateMessage;
use TomasKulhanek\CzechDataBox\DTO\Request\UploadAttachment;
use TomasKulhanek\CzechDataBox\Exception\FileSystemException;
use TomasKulhanek\CzechDataBox\Provider\StreamedRequestBody;
use TomasKulhanek\CzechDataBox\Provider\StreamedRequestBodyStream;
use TomasKulhanek\CzechDataBox\Serializer\AttachmentCollector;
use TomasKulhanek\CzechDataBox\Serializer\SplFileInfo;
use TomasKulhanek\Tests\CzechDataBox\SerializerTrait;

final class StreamedRequestBodyTest extends TestCase
{
    use SerializerTrait;

    /**
     * @return iterable<string, array{int}>
     */
    public static function sizeProvider(): iterable
    {
        // hranice násobků tří i velikosti bloku (3 * 21845 = 65535) hlídají zarovnání base64
        foreach ([0, 1, 2, 3, 4, 100, 65534, 65535, 65536, 65537, 131070, 131071, 200_001] as $size) {
            yield $size . ' B' => [$size];
        }
    }

    #[DataProvider('sizeProvider')]
    public function testStreamedBodyEqualsTheBufferedOne(int $size): void
    {
        $request = $this->uploadRequest(SplFileInfo::createInTemp(self::content($size)));

        [$xml, $collector] = $this->serializeWithCollector($request);
        $body = StreamedRequestBody::create($xml, $collector);

        self::assertFalse($collector->isEmpty());
        $buffered = self::createSerializer()->serialize($request, 'xml');
        self::assertSame($buffered, (string) $body);
        self::assertSame(strlen($buffered), $body->getContentLength());
    }

    public function testSeveralAttachmentsKeepTheirOrder(): void
    {
        $request = new CreateMessage();
        $request->setEnvelope(new Envelope()->setAnnotation('Příloha'))
            ->addRecipient(new Recipient()->setDataBoxId('abcdefg'))
            ->addFile($this->file('a.pdf', self::content(70_000, 1)))
            ->addFile($this->file('b.pdf', self::content(5, 2)))
            ->addFile($this->file('c.pdf', self::content(65_535, 3)));

        [$xml, $collector] = $this->serializeWithCollector($request);
        $body = StreamedRequestBody::create($xml, $collector);

        self::assertCount(3, $collector->getFiles());
        self::assertSame(self::createSerializer()->serialize($request, 'xml'), (string) $body);
    }

    public function testBodyCanBeIteratedRepeatedly(): void
    {
        $body = $this->streamed($this->uploadRequest(SplFileInfo::createInTemp(self::content(100_000))));

        $first = implode('', iterator_to_array($body, false));
        $second = implode('', iterator_to_array($body, false));

        self::assertSame($first, $second);
        self::assertSame((string) $body, $first);
    }

    public function testBodyWithoutAttachmentsIsTheXmlItself(): void
    {
        $body = StreamedRequestBody::create('<xml/>', new AttachmentCollector());

        self::assertSame('<xml/>', (string) $body);
        self::assertSame(6, $body->getContentLength());
    }

    public function testChunksAreSmallerThanTheAttachment(): void
    {
        $body = $this->streamed($this->uploadRequest(SplFileInfo::createInTemp(self::content(1_000_000))));

        $largest = 0;
        foreach ($body as $chunk) {
            $largest = max($largest, strlen($chunk));
        }

        self::assertLessThan(150_000, $largest);
    }

    public function testMissingPlaceholderIsRejected(): void
    {
        $collector = new AttachmentCollector();
        $collector->register(SplFileInfo::createInTemp('x'));

        $this->expectException(FileSystemException::class);

        StreamedRequestBody::create('<xml/>', $collector);
    }

    public function testRepeatedPlaceholderIsRejected(): void
    {
        $collector = new AttachmentCollector();
        $token = $collector->register(SplFileInfo::createInTemp('x'));

        $this->expectException(FileSystemException::class);

        StreamedRequestBody::create('<a>' . $token . '</a><b>' . $token . '</b>', $collector);
    }

    public function testMissingFileIsRejectedWhenTheBodyIsCreated(): void
    {
        $file = SplFileInfo::createInTemp('x');
        $request = $this->uploadRequest($file);
        [$xml, $collector] = $this->serializeWithCollector($request);
        unlink($file->getPathname());

        $this->expectException(FileSystemException::class);

        StreamedRequestBody::create($xml, $collector);
    }

    public function testFileThatShrinksBeforeSendingIsDetected(): void
    {
        $file = SplFileInfo::createInTemp(self::content(100_000));
        $body = $this->streamed($this->uploadRequest($file));

        file_put_contents($file->getPathname(), 'short');

        $this->expectException(FileSystemException::class);
        $this->expectExceptionMessage('changed');

        (string) $body;
    }

    public function testFileThatGrowsBeforeSendingIsDetected(): void
    {
        $file = SplFileInfo::createInTemp(self::content(100_000));
        $body = $this->streamed($this->uploadRequest($file));

        file_put_contents($file->getPathname(), self::content(10), FILE_APPEND);

        $this->expectException(FileSystemException::class);

        (string) $body;
    }

    public function testPartialBodyIsNeverCompleteWhenTheFileChanges(): void
    {
        $file = SplFileInfo::createInTemp(self::content(100_001));
        $body = $this->streamed($this->uploadRequest($file));
        file_put_contents($file->getPathname(), self::content(10), FILE_APPEND);

        $sent = '';
        try {
            foreach ($body as $chunk) {
                $sent .= $chunk;
            }
            self::fail('The changed file must interrupt the body.');
        } catch (FileSystemException) {
            // očekávané
        }

        self::assertLessThan($body->getContentLength(), strlen($sent));
    }

    public function testPsr7StreamReadsTheSameBytes(): void
    {
        $request = $this->uploadRequest(SplFileInfo::createInTemp(self::content(300_000)));
        $body = $this->streamed($request);
        $stream = new StreamedRequestBodyStream($body);

        self::assertSame($body->getContentLength(), $stream->getSize());
        self::assertSame((string) $body, $stream->getContents());
        self::assertTrue($stream->eof());
        self::assertSame($body->getContentLength(), $stream->tell());
    }

    public function testPsr7StreamReadsInSmallPieces(): void
    {
        $body = $this->streamed($this->uploadRequest(SplFileInfo::createInTemp(self::content(10_000))));
        $stream = new StreamedRequestBodyStream($body);

        $read = '';
        while (!$stream->eof()) {
            $read .= $stream->read(7);
        }

        self::assertSame((string) $body, $read);
    }

    public function testPsr7StreamCanBeRewoundAndSeeked(): void
    {
        $body = $this->streamed($this->uploadRequest(SplFileInfo::createInTemp(self::content(50_000))));
        $expected = (string) $body;
        $stream = new StreamedRequestBodyStream($body);

        $stream->read(1000);
        $stream->rewind();
        self::assertSame(0, $stream->tell());
        self::assertSame(substr($expected, 0, 500), $stream->read(500));

        $stream->seek(20_000);
        self::assertSame(substr($expected, 20_000, 300), $stream->read(300));

        $stream->seek(-10, SEEK_END);
        self::assertSame(substr($expected, -10), $stream->getContents());

        $stream->seek(100);
        $stream->seek(50, SEEK_CUR);
        self::assertSame(substr($expected, 150, 10), $stream->read(10));
    }

    public function testPsr7StreamToStringRewindsFirst(): void
    {
        $body = $this->streamed($this->uploadRequest(SplFileInfo::createInTemp(self::content(1000))));
        $stream = new StreamedRequestBodyStream($body);
        $stream->read(100);

        self::assertSame((string) $body, (string) $stream);
    }

    public function testPsr7StreamRejectsSeekingBeyondTheEnd(): void
    {
        $body = $this->streamed($this->uploadRequest(SplFileInfo::createInTemp('abc')));
        $stream = new StreamedRequestBodyStream($body);

        $this->expectException(RuntimeException::class);

        $stream->seek($body->getContentLength() + 1);
    }

    public function testPsr7StreamIsReadOnlyAndCloseable(): void
    {
        $body = $this->streamed($this->uploadRequest(SplFileInfo::createInTemp('abc')));
        $stream = new StreamedRequestBodyStream($body);

        self::assertFalse($stream->isWritable());
        self::assertTrue($stream->isReadable());
        self::assertTrue($stream->isSeekable());

        $stream->close();

        self::assertFalse($stream->isReadable());
        self::assertNull($stream->getSize());
        self::assertSame('', (string) $stream);
    }

    public function testPsr7StreamRefusesWriting(): void
    {
        $body = $this->streamed($this->uploadRequest(SplFileInfo::createInTemp('abc')));

        $this->expectException(RuntimeException::class);

        new StreamedRequestBodyStream($body)->write('x');
    }

    private static function content(int $size, int $seed = 0): string
    {
        $content = '';
        for ($i = 0; strlen($content) < $size; $i++) {
            $content .= hash('sha256', $seed . ':' . $i, true);
        }

        return substr($content, 0, $size);
    }

    private function file(string $description, string $content): File
    {
        return new File()
            ->setMimeType('application/pdf')
            ->setMetaType('enclosure')
            ->setDescription($description)
            ->setEncodedContent(SplFileInfo::createInTemp($content));
    }

    private function uploadRequest(SplFileInfo $content): UploadAttachment
    {
        return new UploadAttachment()->setFile(
            new BigAttachment()
                ->setMimeType('application/pdf')
                ->setDescription('priloha.pdf')
                ->setEncodedContent($content)
        );
    }

    /**
     * @return array{0: string, 1: AttachmentCollector} XML s tokeny místo příloh a kolektor, který je zná
     */
    private function serializeWithCollector(UploadAttachment|CreateMessage $request): array
    {
        $collector = new AttachmentCollector();
        $context = SerializationContext::create()
            ->setAttribute(AttachmentCollector::CONTEXT_ATTRIBUTE, $collector);

        return [self::createSerializer()->serialize($request, 'xml', $context), $collector];
    }

    private function streamed(UploadAttachment|CreateMessage $request): StreamedRequestBody
    {
        [$xml, $collector] = $this->serializeWithCollector($request);

        return StreamedRequestBody::create($xml, $collector);
    }
}
