<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;
use TomasKulhanek\CzechDataBox\Exception\ConnectionException;
use TomasKulhanek\CzechDataBox\Provider\ClientProviderInterface;
use TomasKulhanek\CzechDataBox\Provider\EndpointProvider;
use TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider;
use TomasKulhanek\CzechDataBox\Provider\SymfonyClientProvider;

final class ResponseStreamingMemoryTest extends TestCase
{
    private const int BODY_SIZE = 128 * 1024 * 1024;

    private const int CHUNK_SIZE = 1024 * 1024;

    private const int MAX_RESPONSE_SIZE = 4 * 1024 * 1024;

    private const int PEAK_ALLOWANCE = 32 * 1024 * 1024;

    private const string REQUEST_BODY = '<request/>';

    private function createAccount(): Account
    {
        $account = new Account();
        $account->setLoginName('login')
            ->setPassword('password')
            ->setLoginType(LoginTypeEnum::NAME_PASSWORD);

        return $account;
    }

    private function createGuzzleProvider(StreamInterface $body, bool $announceContentLength): ClientProviderInterface
    {
        $headers = $announceContentLength ? ['Content-Length' => (string) self::BODY_SIZE] : [];

        return new GuzzleClientProvider(
            new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200, $headers, $body)]))]),
            EndpointProvider::test()
        );
    }

    private function createSymfonyProvider(StreamingResponseDouble $response): ClientProviderInterface
    {
        return new SymfonyClientProvider(new StreamingHttpClientDouble($response), EndpointProvider::test());
    }

    private function sendAndExpectRejection(ClientProviderInterface $provider): void
    {
        try {
            $provider->sendRequest(
                $this->createAccount(),
                ServiceTypeEnum::VODZ,
                self::REQUEST_BODY,
                self::MAX_RESPONSE_SIZE
            );
            self::fail('An oversized response must be rejected.');
        } catch (ConnectionException $exception) {
            self::assertStringContainsString('is larger than the allowed', $exception->getMessage());
        }
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

    #[Group('memory')]
    #[RunInSeparateProcess]
    #[DataProvider('backendProvider')]
    public function testOversizedResponseIsAbortedWithoutBufferingItWhole(string $backend): void
    {
        $body = new GeneratedResponseBody(self::BODY_SIZE, self::CHUNK_SIZE);
        $provider = $backend === 'guzzle'
            ? $this->createGuzzleProvider($body->toStream(), false)
            : $this->createSymfonyProvider(new StreamingResponseDouble($body));

        $this->sendAndExpectRejection($provider);

        self::assertLessThanOrEqual(
            self::MAX_RESPONSE_SIZE + 2 * self::CHUNK_SIZE,
            $body->servedBytes,
            'The provider must stop pulling data once the limit is exceeded.'
        );
        self::assertLessThan(
            self::PEAK_ALLOWANCE,
            memory_get_peak_usage(true),
            'The response must never be buffered whole before the size check.'
        );
    }

    #[Group('memory')]
    #[RunInSeparateProcess]
    #[DataProvider('backendProvider')]
    public function testAnnouncedContentLengthIsRejectedBeforeTheBodyIsRead(string $backend): void
    {
        $body = new GeneratedResponseBody(self::BODY_SIZE, self::CHUNK_SIZE);
        $provider = $backend === 'guzzle'
            ? $this->createGuzzleProvider($body->toStream(), true)
            : $this->createSymfonyProvider(
                new StreamingResponseDouble($body, ['content-length' => [(string) self::BODY_SIZE]])
            );

        $this->sendAndExpectRejection($provider);

        self::assertSame(0, $body->servedBytes, 'Content-Length must be checked before the body is read.');
        self::assertLessThan(self::PEAK_ALLOWANCE, memory_get_peak_usage(true));
    }

    #[Group('memory')]
    public function testGuzzleProviderClosesTheBodyOfAnOversizedResponse(): void
    {
        $body = new GeneratedResponseBody(self::BODY_SIZE, self::CHUNK_SIZE);
        $stream = $body->toStream();

        $this->sendAndExpectRejection($this->createGuzzleProvider($stream, false));

        self::assertTrue($stream->eof(), 'The stream of an oversized response must be closed.');
    }

    #[Group('memory')]
    public function testSymfonyProviderCancelsAnOversizedResponse(): void
    {
        $response = new StreamingResponseDouble(new GeneratedResponseBody(self::BODY_SIZE, self::CHUNK_SIZE));

        $this->sendAndExpectRejection($this->createSymfonyProvider($response));

        self::assertTrue($response->canceled, 'An oversized response must be canceled.');
    }
}
