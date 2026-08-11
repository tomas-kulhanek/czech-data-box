<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;
use TomasKulhanek\CzechDataBox\Exception\MissingRequiredField;
use TomasKulhanek\CzechDataBox\Exception\SystemExclusion;
use TomasKulhanek\CzechDataBox\Provider\EndpointProvider;
use TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider;
use TomasKulhanek\CzechDataBox\Provider\SymfonyClientProvider;

/**
 * Both HTTP providers have to be interchangeable: the very same account must result in the very
 * same request on the wire, no matter which HTTP client the application picked.
 */
final class ClientProviderAuthenticationTest extends TestCase
{
    private const string REQUEST_BODY = '<request/>';

    private const string LOGIN_NAME = 'user@example.cz';

    private const string PASSWORD = 'p:a:ss';

    private const string DATA_BOX_ID = 'abcdef1';

    private function createAccount(LoginTypeEnum $loginType): Account
    {
        $account = new Account();
        $account->setLoginType($loginType);
        $account->setLoginName(self::LOGIN_NAME);
        $account->setPassword(self::PASSWORD);
        $account->setDataBoxId(self::DATA_BOX_ID);
        if ($account->usingCertificate()) {
            $account->setPublicKey('-----BEGIN CERTIFICATE-----public-----END CERTIFICATE-----');
            $account->setPrivateKey('-----BEGIN PRIVATE KEY-----private-----END PRIVATE KEY-----');
            $account->setPrivateKeyPassPhrase('passphrase');
        }

        return $account;
    }

    /**
     * Guzzle composes the Authorization header itself out of RequestOptions::AUTH, so the header
     * has to be read from the request the handler received.
     *
     * @return array<string, string> Lower cased header name => header value.
     */
    private function captureGuzzleHeaders(Account $account, ServiceTypeEnum $serviceType): array
    {
        $handler = new MockHandler([new Response(200, [], '<response/>')]);
        $provider = new GuzzleClientProvider(
            new Client(['handler' => HandlerStack::create($handler)]),
            EndpointProvider::test()
        );

        $provider->sendRequest($account, $serviceType, self::REQUEST_BODY);

        $request = $handler->getLastRequest();
        self::assertInstanceOf(RequestInterface::class, $request);

        $headers = [];
        foreach ($request->getHeaders() as $name => $values) {
            $headers[strtolower($name)] = implode(', ', $values);
        }

        return $headers;
    }

    /**
     * @return array<string, string> Lower cased header name => header value.
     */
    private function captureSymfonyHeaders(Account $account, ServiceTypeEnum $serviceType): array
    {
        $headers = [];
        $client = new MockHttpClient(function (string $method, string $url, array $options) use (&$headers): MockResponse {
            $headers = $this->normalizeSymfonyHeaders($options);

            return new MockResponse('<response/>');
        });
        $provider = new SymfonyClientProvider($client, EndpointProvider::test());

        $provider->sendRequest($account, $serviceType, self::REQUEST_BODY);

        self::assertSame(1, $client->getRequestsCount());

        return $headers;
    }

    /**
     * Symfony hands the headers over as "Name: value" lines keyed by the lower cased name.
     *
     * @param array<mixed> $options
     *
     * @return array<string, string>
     */
    private function normalizeSymfonyHeaders(array $options): array
    {
        $normalizedHeaders = $options['normalized_headers'] ?? null;
        if (!is_array($normalizedHeaders)) {
            self::fail('The Symfony client did not normalize any header.');
        }

        $headers = [];
        foreach ($normalizedHeaders as $name => $lines) {
            if (!is_array($lines)) {
                self::fail(sprintf('Header "%s" was not normalized into a list of lines.', (string) $name));
            }
            $values = [];
            foreach ($lines as $line) {
                if (!is_string($line)) {
                    self::fail(sprintf('Header "%s" was not normalized into a string.', (string) $name));
                }
                $values[] = explode(': ', $line, 2)[1] ?? '';
            }
            $headers[strtolower((string) $name)] = implode(', ', $values);
        }

        return $headers;
    }

    /**
     * @return array<string, array{LoginTypeEnum, string}>
     */
    public static function loginTypeProvider(): array
    {
        return [
            'name and password' => [
                LoginTypeEnum::NAME_PASSWORD,
                'Basic ' . base64_encode(self::LOGIN_NAME . ':' . self::PASSWORD),
            ],
            'certificate with login name and password' => [
                LoginTypeEnum::CERT_LOGIN_NAME_PASSWORD,
                'Basic ' . base64_encode(self::LOGIN_NAME . ':' . self::PASSWORD),
            ],
            // RFC 7617 requires the colon separator even when the password is empty.
            'hosted spis' => [
                LoginTypeEnum::HOSTED_SPIS,
                'Basic ' . base64_encode(self::DATA_BOX_ID . ':'),
            ],
            // The client certificate alone authenticates the account, no credentials are sent.
            'certificate only' => [
                LoginTypeEnum::SPIS_CERT,
                '',
            ],
        ];
    }

    #[DataProvider('loginTypeProvider')]
    public function testBothProvidersSendTheSameAuthorizationHeader(LoginTypeEnum $loginType, string $expected): void
    {
        $guzzleHeaders = $this->captureGuzzleHeaders($this->createAccount($loginType), ServiceTypeEnum::INFO);
        $symfonyHeaders = $this->captureSymfonyHeaders($this->createAccount($loginType), ServiceTypeEnum::INFO);

        self::assertSame($expected, $guzzleHeaders['authorization'] ?? '');
        self::assertSame($guzzleHeaders['authorization'] ?? '', $symfonyHeaders['authorization'] ?? '');
    }

    public function testEveryLoginTypeIsCovered(): void
    {
        $covered = array_map(static fn (array $case): LoginTypeEnum => $case[0], self::loginTypeProvider());

        self::assertEqualsCanonicalizing(LoginTypeEnum::cases(), array_values($covered));
    }

    /**
     * @return array<string, array{ServiceTypeEnum, string, string}>
     */
    public static function serviceTypeProvider(): array
    {
        return [
            'operations over soap 1.1' => [ServiceTypeEnum::OPERATIONS, 'text/xml; charset=utf-8', '""'],
            'info over soap 1.1' => [ServiceTypeEnum::INFO, 'text/xml; charset=utf-8', '""'],
            'search over soap 1.1' => [ServiceTypeEnum::SEARCH, 'text/xml; charset=utf-8', '""'],
            'access over soap 1.1' => [ServiceTypeEnum::ACCESS, 'text/xml; charset=utf-8', '""'],
            'vodz over soap 1.2' => [ServiceTypeEnum::VODZ, 'application/soap+xml; charset=utf-8', ''],
            'archive over soap 1.2' => [ServiceTypeEnum::ARCHIVE, 'application/soap+xml; charset=utf-8', ''],
        ];
    }

    #[DataProvider('serviceTypeProvider')]
    public function testBothProvidersSendTheSameSoapHeaders(ServiceTypeEnum $serviceType, string $contentType, string $soapAction): void
    {
        $account = $this->createAccount(LoginTypeEnum::NAME_PASSWORD);
        $guzzleHeaders = $this->captureGuzzleHeaders($account, $serviceType);
        $symfonyHeaders = $this->captureSymfonyHeaders($account, $serviceType);

        self::assertSame($contentType, $guzzleHeaders['content-type'] ?? '');
        self::assertSame($soapAction, $guzzleHeaders['soapaction'] ?? '');
        self::assertSame($guzzleHeaders['content-type'] ?? '', $symfonyHeaders['content-type'] ?? '');
        self::assertSame($guzzleHeaders['soapaction'] ?? '', $symfonyHeaders['soapaction'] ?? '');
    }

    public function testEveryServiceTypeIsCovered(): void
    {
        $covered = array_map(static fn (array $case): ServiceTypeEnum => $case[0], self::serviceTypeProvider());

        self::assertEqualsCanonicalizing(ServiceTypeEnum::cases(), array_values($covered));
    }

    /**
     * @return array<string, array{Account}>
     */
    public static function incompleteAccountProvider(): array
    {
        $withoutDataBoxId = new Account();
        $withoutDataBoxId->setLoginType(LoginTypeEnum::HOSTED_SPIS);

        $withoutPassword = new Account();
        $withoutPassword->setLoginType(LoginTypeEnum::NAME_PASSWORD);
        $withoutPassword->setLoginName(self::LOGIN_NAME);

        $withoutLoginName = new Account();
        $withoutLoginName->setLoginType(LoginTypeEnum::NAME_PASSWORD);
        $withoutLoginName->setPassword(self::PASSWORD);

        return [
            'hosted spis without a data box ID' => [$withoutDataBoxId],
            'password login without a password' => [$withoutPassword],
            'password login without a login name' => [$withoutLoginName],
        ];
    }

    #[DataProvider('incompleteAccountProvider')]
    public function testGuzzleProviderRejectsIncompleteCredentialsBeforeSendingAnything(Account $account): void
    {
        $handler = new MockHandler([new Response(200, [], '<response/>')]);
        $provider = new GuzzleClientProvider(
            new Client(['handler' => HandlerStack::create($handler)]),
            EndpointProvider::test()
        );

        try {
            $provider->sendRequest($account, ServiceTypeEnum::INFO, self::REQUEST_BODY);
            self::fail('Expected a MissingRequiredField to be thrown.');
        } catch (MissingRequiredField) {
            self::assertNull($handler->getLastRequest(), 'Incomplete credentials must never reach ISDS.');
        }
    }

    #[DataProvider('incompleteAccountProvider')]
    public function testSymfonyProviderRejectsIncompleteCredentialsBeforeSendingAnything(Account $account): void
    {
        $client = new MockHttpClient(new MockResponse('<response/>'));
        $provider = new SymfonyClientProvider($client, EndpointProvider::test());

        try {
            $provider->sendRequest($account, ServiceTypeEnum::INFO, self::REQUEST_BODY);
            self::fail('Expected a MissingRequiredField to be thrown.');
        } catch (MissingRequiredField) {
            self::assertSame(0, $client->getRequestsCount(), 'Incomplete credentials must never reach ISDS.');
        }
    }

    public function testGuzzleProviderMapsHttp503ToSystemExclusion(): void
    {
        $provider = new GuzzleClientProvider(
            new Client(['handler' => HandlerStack::create(new MockHandler([new Response(503, [], 'Service Temporarily Unavailable')]))]),
            EndpointProvider::test()
        );

        $this->expectException(SystemExclusion::class);
        $provider->sendRequest($this->createAccount(LoginTypeEnum::NAME_PASSWORD), ServiceTypeEnum::INFO, self::REQUEST_BODY);
    }

    public function testSymfonyProviderMapsHttp503ToSystemExclusion(): void
    {
        $provider = new SymfonyClientProvider(
            new MockHttpClient(new MockResponse('Service Temporarily Unavailable', ['http_code' => 503])),
            EndpointProvider::test()
        );

        $this->expectException(SystemExclusion::class);
        $provider->sendRequest($this->createAccount(LoginTypeEnum::NAME_PASSWORD), ServiceTypeEnum::INFO, self::REQUEST_BODY);
    }
}
