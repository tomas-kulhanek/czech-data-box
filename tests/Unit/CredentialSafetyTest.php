<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SensitiveParameterValue;
use Throwable;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;
use TomasKulhanek\CzechDataBox\Exception\ConnectionException;
use TomasKulhanek\CzechDataBox\Exception\MissingRequiredField;
use TomasKulhanek\CzechDataBox\Exception\PkcsCertificateException;
use TomasKulhanek\CzechDataBox\Provider\EndpointProvider;
use TomasKulhanek\CzechDataBox\Provider\GuzzleClientProvider;
use TomasKulhanek\CzechDataBox\Provider\SymfonyClientProvider;

final class CredentialSafetyTest extends TestCase
{
    use GeneratePkcs;

    private const string LOGIN_NAME = 'ovmuser';

    private const string PASSWORD = 'sup3rsecretpassw0rd';

    private const string PASS_PHRASE = 'sup3rsecretpassphrase';

    private ?string $ignoreArgs = null;

    protected function setUp(): void
    {
        $current = ini_get('zend.exception_ignore_args');
        $this->ignoreArgs = $current === false ? null : $current;
        ini_set('zend.exception_ignore_args', '0');
    }

    protected function tearDown(): void
    {
        if ($this->ignoreArgs !== null) {
            ini_set('zend.exception_ignore_args', $this->ignoreArgs);
        }
    }

    private function createAccount(): Account
    {
        $account = new Account();
        $account->setLoginType(LoginTypeEnum::NAME_PASSWORD);
        $account->setLoginName(self::LOGIN_NAME);
        $account->setPassword(self::PASSWORD);

        return $account;
    }

    /**
     * @return list<string>
     */
    private static function secrets(): array
    {
        return [
            self::PASSWORD,
            self::PASS_PHRASE,
            base64_encode(self::LOGIN_NAME . ':' . self::PASSWORD),
        ];
    }

    private static function assertHoldsNoSecret(string $dump, string $what): void
    {
        foreach (self::secrets() as $secret) {
            self::assertStringNotContainsString($secret, $dump, sprintf('%s leaks a credential.', $what));
        }
    }

    public function testVarDumpDoesNotRevealCredentials(): void
    {
        $account = $this->createAccount();
        $account->setPrivateKey('-----BEGIN PRIVATE KEY-----' . self::PASS_PHRASE . '-----END PRIVATE KEY-----');
        $account->setPrivateKeyPassPhrase(self::PASS_PHRASE);

        ob_start();
        var_dump($account);
        $dump = (string) ob_get_clean();

        self::assertHoldsNoSecret($dump, 'var_dump() of the account');
        self::assertStringContainsString('***', $dump);
        self::assertStringContainsString(self::LOGIN_NAME, $dump, 'Non sensitive properties stay readable.');
    }

    public function testPrintRDoesNotRevealCredentials(): void
    {
        $account = $this->createAccount();
        $account->setPrivateKeyPassPhrase(self::PASS_PHRASE);

        self::assertHoldsNoSecret(print_r($account, true), 'print_r() of the account');
    }

    public function testDebugInfoKeepsNonSensitivePropertiesReadable(): void
    {
        $account = $this->createAccount();
        $account->setDataBoxId('abc1234');
        $account->setPublicKey('-----BEGIN CERTIFICATE-----');
        $account->setPrivateKeyPassPhrase(self::PASS_PHRASE);

        $debugInfo = $account->__debugInfo();

        self::assertSame(self::LOGIN_NAME, $debugInfo['loginName']);
        self::assertSame('abc1234', $debugInfo['dataBoxId']);
        self::assertSame(LoginTypeEnum::NAME_PASSWORD, $debugInfo['loginType']);
        self::assertSame('-----BEGIN CERTIFICATE-----', $debugInfo['publicKey']);
        self::assertSame('***', $debugInfo['password']);
        self::assertNull($debugInfo['privateKey'], 'An unset secret stays null instead of pretending to hold a value.');
        self::assertSame('***', $debugInfo['privateKeyPassPhrase']);
    }

    public function testPkcs12FailureDoesNotExposeTheBundleNorItsPassPhrase(): void
    {
        $account = new Account();

        try {
            $account->setPkcs12Certificate('this is not a PKCS12 bundle', self::PASS_PHRASE);
            self::fail('Expected a PkcsCertificateException to be thrown.');
        } catch (PkcsCertificateException $exception) {
            self::assertHoldsNoSecret($exception->getTraceAsString(), 'The PKCS12 stack trace');
            self::assertHoldsNoSecret(print_r($exception->getTrace(), true), 'The PKCS12 trace array');
            self::assertHoldsNoSecret((string) $exception, 'The PKCS12 exception cast to string');

            $arguments = $this->argumentsOfFrame($exception, 'setPkcs12Certificate');
            self::assertCount(2, $arguments);
            foreach ($arguments as $argument) {
                self::assertInstanceOf(SensitiveParameterValue::class, $argument);
            }
        }
    }

    public function testPkcs12PassPhraseIsHiddenEvenWhenTheBundleIsValid(): void
    {
        $account = new Account();
        $account->setPkcs12Certificate($this->generateP12Certificate(self::PASS_PHRASE), self::PASS_PHRASE);

        self::assertSame(self::PASS_PHRASE, $account->getPrivateKeyPassPhrase(), 'The value is still usable.');
        self::assertHoldsNoSecret(print_r($account, true), 'print_r() of the account built from PKCS12');
    }

    /**
     * @return array<int, mixed>
     */
    private function argumentsOfFrame(Throwable $exception, string $function): array
    {
        foreach ($exception->getTrace() as $frame) {
            if ($frame['function'] === $function) {
                return $frame['args'] ?? [];
            }
        }

        self::fail(sprintf('No %s() frame found in the stack trace.', $function));
    }

    public function testGuzzleProviderDoesNotChainTheGuzzleException(): void
    {
        $handler = HandlerStack::create(new MockHandler([new Response(500, [], '')]));
        $provider = new GuzzleClientProvider(new Client(['handler' => $handler]), EndpointProvider::test());

        try {
            $provider->sendRequest($this->createAccount(), ServiceTypeEnum::INFO, '<request/>');
            self::fail('Expected a ConnectionException to be thrown.');
        } catch (ConnectionException $exception) {
            self::assertNull(
                $exception->getPrevious(),
                'The Guzzle exception carries the request with the Authorization header and must not be chained.'
            );
            self::assertSame(500, $exception->getCode());
            self::assertStringContainsString(ServerException::class, $exception->getMessage());
            self::assertStringContainsString('500', $exception->getMessage());
            self::assertHoldsNoSecret(print_r($exception, true), 'The connection exception');
        }
    }

    public function testGuzzleProviderDoesNotChainTheGuzzleExceptionOnSystemExclusion(): void
    {
        $handler = HandlerStack::create(new MockHandler([new Response(503, [], '')]));
        $provider = new GuzzleClientProvider(new Client(['handler' => $handler]), EndpointProvider::test());

        try {
            $provider->sendRequest($this->createAccount(), ServiceTypeEnum::INFO, '<request/>');
            self::fail('Expected a SystemExclusion to be thrown.');
        } catch (Throwable $exception) {
            self::assertNull($exception->getPrevious());
            self::assertHoldsNoSecret(print_r($exception, true), 'The system exclusion');
        }
    }

    #[DataProvider('missingFieldProvider')]
    public function testMissingRequiredFieldNamesTheField(Account $account, string $expectedMessage): void
    {
        $provider = new GuzzleClientProvider(new Client(), EndpointProvider::test());

        $this->expectException(MissingRequiredField::class);
        $this->expectExceptionMessage($expectedMessage);
        $provider->sendRequest($account, ServiceTypeEnum::INFO, '<request/>');
    }

    /**
     * @return iterable<string, array{Account, string}>
     */
    public static function missingFieldProvider(): iterable
    {
        $hostedSpis = new Account();
        $hostedSpis->setLoginType(LoginTypeEnum::HOSTED_SPIS);
        yield 'hosted spis without a data box ID' => [$hostedSpis, 'The required field \'dbID\' is empty.'];

        $noLogin = new Account();
        $noLogin->setLoginType(LoginTypeEnum::NAME_PASSWORD);
        yield 'no login name' => [$noLogin, 'The required field \'loginName\' is empty.'];

        $noPassword = new Account();
        $noPassword->setLoginType(LoginTypeEnum::NAME_PASSWORD);
        $noPassword->setLoginName(self::LOGIN_NAME);
        yield 'no password' => [$noPassword, 'The required field \'password\' is empty.'];

        $noPem = new Account();
        $noPem->setLoginType(LoginTypeEnum::SPIS_CERT);
        yield 'no PEM data' => [$noPem, 'The required field \'publicKey\' is empty.'];

        $noPrivateKey = new Account();
        $noPrivateKey->setLoginType(LoginTypeEnum::SPIS_CERT);
        $noPrivateKey->setPublicKey('-----BEGIN CERTIFICATE-----');
        yield 'no private key' => [$noPrivateKey, 'The required field \'privateKey\' is empty.'];
    }

    public function testSymfonyProviderReportsTheMissingPemField(): void
    {
        $account = new Account();
        $account->setLoginType(LoginTypeEnum::SPIS_CERT);
        $account->setPublicKey('-----BEGIN CERTIFICATE-----');
        $provider = SymfonyClientProvider::create(EndpointProvider::test());

        $this->expectException(MissingRequiredField::class);
        $this->expectExceptionMessage('The required field \'privateKey\' is empty.');
        $provider->sendRequest($account, ServiceTypeEnum::INFO, '<request/>');
    }
}
