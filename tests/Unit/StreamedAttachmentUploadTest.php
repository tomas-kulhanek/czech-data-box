<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use RuntimeException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\DTO\BigAttachment;
use TomasKulhanek\CzechDataBox\DTO\Request\UploadAttachment;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Exception\FileSystemException;
use TomasKulhanek\CzechDataBox\Provider\EndpointProvider;
use TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider;
use TomasKulhanek\CzechDataBox\Provider\SymfonyClientProvider;
use TomasKulhanek\CzechDataBox\Serializer\SplFileInfo;
use TomasKulhanek\Tests\CzechDataBox\SerializerTrait;

final class StreamedAttachmentUploadTest extends TestCase
{
    use SerializerTrait;

    private const string SOAP_RESPONSE = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
  <soap:Body>
    <p:UploadAttachmentResponse xmlns:p="http://isds.czechpoint.cz/v20">
      <p:dmAttID>ATT123456</p:dmAttID>
      <p:dmStatus>
        <p:dmStatusCode>0000</p:dmStatusCode>
        <p:dmStatusMessage>Operation successfully</p:dmStatusMessage>
      </p:dmStatus>
    </p:UploadAttachmentResponse>
  </soap:Body>
</soap:Envelope>
XML;

    public function testStreamResourceBecomesATemporaryFile(): void
    {
        $stream = fopen('php://memory', 'w+b');
        self::assertNotFalse($stream);
        fwrite($stream, 'obsah přílohy');
        rewind($stream);

        $file = SplFileInfo::createFromStream($stream);

        self::assertTrue($file->isTemporary());
        self::assertSame('obsah přílohy', $file->getContents());
        self::assertTrue(is_resource($stream), 'The stream must stay open for the caller.');
        fclose($stream);
    }

    public function testStreamIsReadFromItsCurrentPosition(): void
    {
        $stream = tmpfile();
        self::assertNotFalse($stream);
        fwrite($stream, 'ignored|payload');
        fseek($stream, 8);

        self::assertSame('payload', SplFileInfo::createFromStream($stream)->getContents());
    }

    public function testLargeStreamIsCopiedInBlocks(): void
    {
        $stream = tmpfile();
        self::assertNotFalse($stream);
        $block = str_repeat('0123456789abcdef', 65_536);
        for ($i = 0; $i < 5; $i++) {
            fwrite($stream, $block);
        }
        rewind($stream);

        $file = SplFileInfo::createFromStream($stream);

        self::assertSame(5 * strlen($block), $file->getSize());
        self::assertSame(hash('sha1', str_repeat($block, 5)), sha1_file($file->getPathname()));
    }

    public function testEmptyStreamGivesAnEmptyFile(): void
    {
        $stream = fopen('php://memory', 'r+b');
        self::assertNotFalse($stream);

        $file = SplFileInfo::createFromStream($stream);

        self::assertSame(0, $file->getSize());
    }

    public function testPsr7StreamIsAccepted(): void
    {
        $file = SplFileInfo::createFromStream(Utils::streamFor(str_repeat('ž', 3_000_000)));

        self::assertSame(6_000_000, $file->getSize());
        self::assertTrue($file->isTemporary());
    }

    public function testTemporaryCopyIsRemovedWithTheObject(): void
    {
        $stream = fopen('php://memory', 'r+b');
        self::assertNotFalse($stream);
        fwrite($stream, 'x');
        rewind($stream);
        $file = SplFileInfo::createFromStream($stream);
        $path = $file->getPathname();

        unset($file);

        self::assertFileDoesNotExist($path);
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function notAStreamProvider(): iterable
    {
        yield 'path to a file' => ['/cesta/k/souboru.pdf'];
        yield 'file object' => [new \SplFileInfo(__FILE__)];
        yield 'null' => [null];
        yield 'int' => [42];
    }

    #[DataProvider('notAStreamProvider')]
    public function testSomethingThatIsNotAStreamIsRejected(mixed $notAStream): void
    {
        $this->expectException(RuntimeException::class);

        SplFileInfo::createFromStream($notAStream);
    }

    public function testClosedStreamIsRejected(): void
    {
        $stream = fopen('php://memory', 'r+b');
        self::assertNotFalse($stream);
        fclose($stream);

        $this->expectException(RuntimeException::class);

        SplFileInfo::createFromStream($stream);
    }

    public function testConnectorHandsAttachmentsToAStreamingProviderUnexpanded(): void
    {
        $content = random_bytes(200_000);
        $request = $this->uploadRequest(SplFileInfo::createInTemp($content));

        $streaming = new StreamingRecordingClientProvider(self::SOAP_RESPONSE);
        new Connector(self::createSerializer(), $streaming)->uploadAttachment($this->account(), $request);
        $buffered = new RecordingClientProvider(self::SOAP_RESPONSE);
        new Connector(self::createSerializer(), $buffered)->uploadAttachment($this->account(), $request);

        self::assertNull($streaming->bufferedBody, 'Attachments must not go through the buffered method.');
        self::assertNotNull($streaming->streamedBody);
        self::assertNotNull($buffered->capturedBody);
        self::assertSame($buffered->capturedBody, (string) $streaming->streamedBody);
        self::assertSame(strlen($buffered->capturedBody), $streaming->streamedBody->getContentLength());
        self::assertStringContainsString(base64_encode($content), (string) $streaming->streamedBody);
    }

    public function testRequestWithoutAttachmentsStaysBuffered(): void
    {
        $provider = new StreamingRecordingClientProvider(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body>
<p:DummyOperationResponse xmlns:p="http://isds.czechpoint.cz/v20"/>
</soap:Body></soap:Envelope>
XML);

        new Connector(self::createSerializer(), $provider)->dummyOperation($this->account());

        self::assertNotNull($provider->bufferedBody);
        self::assertNull($provider->streamedBody);
    }

    public function testGuzzleSendsAnIdenticalBodyWithKnownLength(): void
    {
        $content = random_bytes(1_500_000);
        $request = $this->uploadRequest(SplFileInfo::createInTemp($content));
        $reference = new RecordingClientProvider(self::SOAP_RESPONSE);
        new Connector(self::createSerializer(), $reference)->uploadAttachment($this->account(), $request);

        $sent = '';
        $headers = [];
        $handler = new MockHandler([static function (RequestInterface $request) use (&$sent, &$headers): Response {
            $headers = $request->getHeaders();
            RequestBodyDrain::psr7($request->getBody(), static function (string $chunk) use (&$sent): void {
                $sent .= $chunk;
            });

            return new Response(200, [], self::SOAP_RESPONSE);
        }]);
        $provider = new GuzzleClientProvider(new Client(['handler' => HandlerStack::create($handler)]), EndpointProvider::test());

        $response = new Connector(self::createSerializer(), $provider)->uploadAttachment($this->account(), $request);

        self::assertTrue($response->getStatus()->isOk());
        self::assertSame($reference->capturedBody, $sent);
        self::assertSame([(string) strlen((string) $reference->capturedBody)], $headers['Content-Length']);
        self::assertArrayNotHasKey('Transfer-Encoding', $headers);
    }

    public function testGuzzleBodyUsesTheCurlUploadPathForLargeAttachments(): void
    {
        $content = random_bytes(1_500_000);
        $request = $this->uploadRequest(SplFileInfo::createInTemp($content));
        $size = null;
        $seekable = null;
        $handler = new MockHandler([static function (RequestInterface $request) use (&$size, &$seekable): Response {
            $size = $request->getBody()->getSize();
            $seekable = $request->getBody()->isSeekable();

            return new Response(200, [], self::SOAP_RESPONSE);
        }]);
        $provider = new GuzzleClientProvider(new Client(['handler' => HandlerStack::create($handler)]), EndpointProvider::test());

        new Connector(self::createSerializer(), $provider)->uploadAttachment($this->account(), $request);

        self::assertGreaterThan(2_000_000, $size);
        self::assertTrue($seekable, 'A seekable body lets Guzzle replay the request on a stale connection.');
    }

    public function testSymfonySendsAnIdenticalBodyWithKnownLength(): void
    {
        $content = random_bytes(1_500_000);
        $request = $this->uploadRequest(SplFileInfo::createInTemp($content));
        $reference = new RecordingClientProvider(self::SOAP_RESPONSE);
        new Connector(self::createSerializer(), $reference)->uploadAttachment($this->account(), $request);

        $sent = '';
        $contentLength = null;
        $headerNames = [];
        $client = new MockHttpClient(
            static function (string $method, string $url, array $options) use (&$sent, &$contentLength, &$headerNames): MockResponse {
                $headers = $options['normalized_headers'] ?? [];
                self::assertIsArray($headers);
                $headerNames = array_keys($headers);
                $lengths = $headers['content-length'] ?? [];
                self::assertIsArray($lengths);
                $contentLength = $lengths[0] ?? null;
                RequestBodyDrain::symfony($options['body'] ?? null, static function (string $chunk) use (&$sent): void {
                    $sent .= $chunk;
                });

                return new MockResponse(self::SOAP_RESPONSE);
            }
        );
        $provider = new SymfonyClientProvider($client, EndpointProvider::test());

        $response = new Connector(self::createSerializer(), $provider)->uploadAttachment($this->account(), $request);

        self::assertTrue($response->getStatus()->isOk());
        self::assertSame($reference->capturedBody, $sent);
        self::assertSame('Content-Length: ' . strlen((string) $reference->capturedBody), $contentLength);
        self::assertContains('content-length', $headerNames);
        self::assertNotContains('transfer-encoding', $headerNames, 'A known length must not fall back to chunked encoding.');
    }

    public function testFileChangedDuringTheUploadIsReportedByGuzzle(): void
    {
        $file = SplFileInfo::createInTemp(random_bytes(2_000_000));
        $request = $this->uploadRequest($file);
        $handler = new MockHandler([static function (RequestInterface $request) use ($file): Response {
            file_put_contents($file->getPathname(), 'short');
            RequestBodyDrain::psr7($request->getBody(), static function (string $chunk): void {
            });

            return new Response(200, [], self::SOAP_RESPONSE);
        }]);
        $provider = new GuzzleClientProvider(new Client(['handler' => HandlerStack::create($handler)]), EndpointProvider::test());

        $this->expectException(FileSystemException::class);

        new Connector(self::createSerializer(), $provider)->uploadAttachment($this->account(), $request);
    }

    private function account(): Account
    {
        return new Account()
            ->setLoginName('login')
            ->setPassword('password')
            ->setLoginType(LoginTypeEnum::NAME_PASSWORD);
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
}
