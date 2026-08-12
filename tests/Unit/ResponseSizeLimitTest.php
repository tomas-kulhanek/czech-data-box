<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\DTO\Request\MessageEnvelopeDownload;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;
use TomasKulhanek\CzechDataBox\Exception\ConnectionException;
use TomasKulhanek\CzechDataBox\Provider\ClientProviderInterface;
use TomasKulhanek\CzechDataBox\Provider\EndpointProvider;
use TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider;
use TomasKulhanek\CzechDataBox\Provider\ResponseSizeLimit;
use TomasKulhanek\CzechDataBox\Provider\SymfonyClientProvider;
use TomasKulhanek\Tests\CzechDataBox\SerializerTrait;

final class ResponseSizeLimitTest extends TestCase
{
    use SerializerTrait;

    private const int MAX_RESPONSE_SIZE = 1024;

    private const int OVERSIZED_RESPONSE_SIZE = 4096;

    private const int CHUNK_SIZE = 512;

    private const string REQUEST_BODY = '<request/>';

    private const string EXPECTED_MESSAGE = 'The response is larger than the allowed 1.0 kB.';

    private function createAccount(): Account
    {
        $account = new Account();
        $account->setLoginName('login')
            ->setPassword('password')
            ->setLoginType(LoginTypeEnum::NAME_PASSWORD);

        return $account;
    }

    /**
     * @return list<string>
     */
    private static function chunks(string $body): array
    {
        return str_split($body, self::CHUNK_SIZE);
    }

    private function createGuzzleProvider(string $body, bool $announceContentLength): ClientProviderInterface
    {
        $headers = $announceContentLength ? ['Content-Length' => (string) strlen($body)] : [];

        return new GuzzleClientProvider(
            new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200, $headers, $body)]))]),
            EndpointProvider::test()
        );
    }

    private function createSymfonyProvider(string $body, bool $announceContentLength): ClientProviderInterface
    {
        $response = $announceContentLength
            ? new MockResponse($body, ['response_headers' => ['content-length' => (string) strlen($body)]])
            : new MockResponse(self::chunks($body));

        return new SymfonyClientProvider(new MockHttpClient($response), EndpointProvider::test());
    }

    private function createProvider(string $backend, string $body, bool $announceContentLength): ClientProviderInterface
    {
        return match ($backend) {
            'guzzle' => $this->createGuzzleProvider($body, $announceContentLength),
            'symfony' => $this->createSymfonyProvider($body, $announceContentLength),
            default => self::fail(sprintf('Unknown backend "%s".', $backend)),
        };
    }

    private function send(string $backend, string $body, bool $announceContentLength, ?int $maxResponseSize): string
    {
        return $this->createProvider($backend, $body, $announceContentLength)
            ->sendRequest($this->createAccount(), ServiceTypeEnum::INFO, self::REQUEST_BODY, $maxResponseSize);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function backendProvider(): array
    {
        return [
            'guzzle' => ['guzzle'],
            'symfony' => ['symfony'],
        ];
    }

    #[DataProvider('backendProvider')]
    public function testAnnouncedContentLengthOverTheLimitIsRejected(string $backend): void
    {
        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage(self::EXPECTED_MESSAGE);

        $this->send($backend, str_repeat('a', self::OVERSIZED_RESPONSE_SIZE), true, self::MAX_RESPONSE_SIZE);
    }

    #[DataProvider('backendProvider')]
    public function testChunkedResponseWithoutContentLengthIsRejected(string $backend): void
    {
        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage(self::EXPECTED_MESSAGE);

        $this->send($backend, str_repeat('a', self::OVERSIZED_RESPONSE_SIZE), false, self::MAX_RESPONSE_SIZE);
    }

    #[DataProvider('backendProvider')]
    public function testResponseUnderTheLimitIsReturnedUnchanged(string $backend): void
    {
        $body = str_repeat('a', self::MAX_RESPONSE_SIZE);

        self::assertSame($body, $this->send($backend, $body, true, self::MAX_RESPONSE_SIZE));
        self::assertSame($body, $this->send($backend, $body, false, self::MAX_RESPONSE_SIZE));
    }

    #[DataProvider('backendProvider')]
    public function testResponseIsNotTruncatedWhenNoLimitIsGiven(string $backend): void
    {
        $body = str_repeat('a', self::OVERSIZED_RESPONSE_SIZE);

        self::assertSame($body, $this->send($backend, $body, true, null));
        self::assertSame($body, $this->send($backend, $body, false, null));
    }

    /**
     * @return array<string, array{bool}>
     */
    public static function contentLengthProvider(): array
    {
        return [
            'with content length' => [true],
            'without content length' => [false],
        ];
    }

    #[DataProvider('contentLengthProvider')]
    public function testBothProvidersRejectTheSameResponseWithTheSameException(bool $announceContentLength): void
    {
        $body = str_repeat('a', self::OVERSIZED_RESPONSE_SIZE);

        $failures = [];
        foreach (array_keys(self::backendProvider()) as $backend) {
            try {
                $this->send($backend, $body, $announceContentLength, self::MAX_RESPONSE_SIZE);
                self::fail(sprintf('The %s provider accepted an oversized response.', $backend));
            } catch (ConnectionException $exception) {
                $failures[$backend] = $exception::class . ': ' . $exception->getMessage();
            }
        }

        self::assertSame(
            [
                'guzzle' => ConnectionException::class . ': ' . self::EXPECTED_MESSAGE,
                'symfony' => ConnectionException::class . ': ' . self::EXPECTED_MESSAGE,
            ],
            $failures
        );
    }

    public function testConnectorPassesItsLimitToTheProvider(): void
    {
        $soapResponse = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
  <SOAP-ENV:Body>
    <p:MessageEnvelopeDownloadResponse xmlns:p="http://isds.czechpoint.cz/v20">
      <p:dmStatus>
        <p:dmStatusCode>0000</p:dmStatusCode>
        <p:dmStatusMessage>Operation successfully</p:dmStatusMessage>
      </p:dmStatus>
    </p:MessageEnvelopeDownloadResponse>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;
        $provider = new RecordingClientProvider($soapResponse);
        $request = new MessageEnvelopeDownload();
        $request->setDataMessageId('123456789');

        new Connector(self::createSerializer(), $provider, 4096)
            ->messageEnvelopeDownload($this->createAccount(), $request);

        self::assertSame(4096, $provider->capturedMaxResponseSize);
    }

    public function testConnectorKeepsTheDocumentedDefaultLimit(): void
    {
        self::assertSame(ResponseSizeLimit::DEFAULT_MAX_RESPONSE_SIZE, Connector::DEFAULT_MAX_RESPONSE_SIZE);
        self::assertSame(256 * 1024 ** 2, Connector::DEFAULT_MAX_RESPONSE_SIZE);
    }
}
