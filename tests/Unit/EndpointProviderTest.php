<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;
use TomasKulhanek\CzechDataBox\Exception\InvalidEndpointDomain;
use TomasKulhanek\CzechDataBox\Provider\EndpointProvider;

class EndpointProviderTest extends TestCase
{
    private function createAccount(LoginTypeEnum $loginType): Account
    {
        $account = new Account();
        $account->setLoginType($loginType);
        return $account;
    }

    public function testInfoServices(): void
    {
        $account = $this->createAccount(LoginTypeEnum::NAME_PASSWORD);
        self::assertSame('https://ws1.datovka-test.gov.cz/DS/dx', EndpointProvider::test()->getServiceLocation($account, ServiceTypeEnum::INFO));
        self::assertSame('https://ws1.datovka.gov.cz/DS/dx', EndpointProvider::production()->getServiceLocation($account, ServiceTypeEnum::INFO));

        $account = $this->createAccount(LoginTypeEnum::SPIS_CERT);
        self::assertSame('https://ws1c.datovka-test.gov.cz/cert/DS/dx', EndpointProvider::test()->getServiceLocation($account, ServiceTypeEnum::INFO));
        self::assertSame('https://ws1c.datovka.gov.cz/cert/DS/dx', EndpointProvider::production()->getServiceLocation($account, ServiceTypeEnum::INFO));

        $account = $this->createAccount(LoginTypeEnum::CERT_LOGIN_NAME_PASSWORD);
        self::assertSame('https://ws1c.datovka-test.gov.cz/certds/DS/dx', EndpointProvider::test()->getServiceLocation($account, ServiceTypeEnum::INFO));
        self::assertSame('https://ws1c.datovka.gov.cz/certds/DS/dx', EndpointProvider::production()->getServiceLocation($account, ServiceTypeEnum::INFO));
    }

    public function testDefaultConstructorIsProduction(): void
    {
        $account = $this->createAccount(LoginTypeEnum::NAME_PASSWORD);
        $endpointProvider = new EndpointProvider();
        self::assertSame('https://ws1.datovka.gov.cz/DS/dx', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::INFO));
    }

    public function testCustomDomain(): void
    {
        $account = $this->createAccount(LoginTypeEnum::NAME_PASSWORD);

        $endpointProvider = new EndpointProvider('datovka.cms2.cz');
        self::assertSame('https://ws1.datovka.cms2.cz/DS/dx', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::INFO));
        self::assertSame('https://ws2.datovka.cms2.cz/DS/vodz', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::VODZ));

        $endpointProvider = new EndpointProvider('czebox.cz');
        self::assertSame('https://ws1.czebox.cz/DS/dz', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::OPERATIONS));
    }

    public function testAccessServices(): void
    {
        $account = $this->createAccount(LoginTypeEnum::NAME_PASSWORD);
        self::assertSame('https://ws1.datovka-test.gov.cz/DS/DsManage', EndpointProvider::test()->getServiceLocation($account, ServiceTypeEnum::ACCESS));
        self::assertSame('https://ws1.datovka.gov.cz/DS/DsManage', EndpointProvider::production()->getServiceLocation($account, ServiceTypeEnum::ACCESS));

        $account = $this->createAccount(LoginTypeEnum::SPIS_CERT);
        self::assertSame('https://ws1c.datovka-test.gov.cz/cert/DS/DsManage', EndpointProvider::test()->getServiceLocation($account, ServiceTypeEnum::ACCESS));
        self::assertSame('https://ws1c.datovka.gov.cz/cert/DS/DsManage', EndpointProvider::production()->getServiceLocation($account, ServiceTypeEnum::ACCESS));

        $account = $this->createAccount(LoginTypeEnum::CERT_LOGIN_NAME_PASSWORD);
        self::assertSame('https://ws1c.datovka-test.gov.cz/certds/DS/DsManage', EndpointProvider::test()->getServiceLocation($account, ServiceTypeEnum::ACCESS));
        self::assertSame('https://ws1c.datovka.gov.cz/certds/DS/DsManage', EndpointProvider::production()->getServiceLocation($account, ServiceTypeEnum::ACCESS));
    }

    public function testSearchServices(): void
    {
        $account = $this->createAccount(LoginTypeEnum::NAME_PASSWORD);
        self::assertSame('https://ws1.datovka-test.gov.cz/DS/df', EndpointProvider::test()->getServiceLocation($account, ServiceTypeEnum::SEARCH));
        self::assertSame('https://ws1.datovka.gov.cz/DS/df', EndpointProvider::production()->getServiceLocation($account, ServiceTypeEnum::SEARCH));

        $account = $this->createAccount(LoginTypeEnum::SPIS_CERT);
        self::assertSame('https://ws1c.datovka-test.gov.cz/cert/DS/df', EndpointProvider::test()->getServiceLocation($account, ServiceTypeEnum::SEARCH));
        self::assertSame('https://ws1c.datovka.gov.cz/cert/DS/df', EndpointProvider::production()->getServiceLocation($account, ServiceTypeEnum::SEARCH));

        $account = $this->createAccount(LoginTypeEnum::CERT_LOGIN_NAME_PASSWORD);
        self::assertSame('https://ws1c.datovka-test.gov.cz/certds/DS/df', EndpointProvider::test()->getServiceLocation($account, ServiceTypeEnum::SEARCH));
        self::assertSame('https://ws1c.datovka.gov.cz/certds/DS/df', EndpointProvider::production()->getServiceLocation($account, ServiceTypeEnum::SEARCH));
    }

    public function testVodzServices(): void
    {
        $account = $this->createAccount(LoginTypeEnum::NAME_PASSWORD);
        self::assertSame('https://ws2.datovka-test.gov.cz/DS/vodz', EndpointProvider::test()->getServiceLocation($account, ServiceTypeEnum::VODZ));
        self::assertSame('https://ws2.datovka.gov.cz/DS/vodz', EndpointProvider::production()->getServiceLocation($account, ServiceTypeEnum::VODZ));

        $account = $this->createAccount(LoginTypeEnum::SPIS_CERT);
        self::assertSame('https://ws2c.datovka-test.gov.cz/cert/DS/vodz', EndpointProvider::test()->getServiceLocation($account, ServiceTypeEnum::VODZ));
        self::assertSame('https://ws2c.datovka.gov.cz/cert/DS/vodz', EndpointProvider::production()->getServiceLocation($account, ServiceTypeEnum::VODZ));

        $account = $this->createAccount(LoginTypeEnum::CERT_LOGIN_NAME_PASSWORD);
        self::assertSame('https://ws2c.datovka-test.gov.cz/certds/DS/vodz', EndpointProvider::test()->getServiceLocation($account, ServiceTypeEnum::VODZ));
        self::assertSame('https://ws2c.datovka.gov.cz/certds/DS/vodz', EndpointProvider::production()->getServiceLocation($account, ServiceTypeEnum::VODZ));

        $account = $this->createAccount(LoginTypeEnum::HOSTED_SPIS);
        self::assertSame('https://ws2c.datovka-test.gov.cz/hspis/DS/vodz', EndpointProvider::test()->getServiceLocation($account, ServiceTypeEnum::VODZ));
        self::assertSame('https://ws2c.datovka.gov.cz/hspis/DS/vodz', EndpointProvider::production()->getServiceLocation($account, ServiceTypeEnum::VODZ));
    }

    public function testArchiveServices(): void
    {
        $account = $this->createAccount(LoginTypeEnum::NAME_PASSWORD);
        self::assertSame('https://ws2.datovka-test.gov.cz/DS/arch', EndpointProvider::test()->getServiceLocation($account, ServiceTypeEnum::ARCHIVE));
        self::assertSame('https://ws2.datovka.gov.cz/DS/arch', EndpointProvider::production()->getServiceLocation($account, ServiceTypeEnum::ARCHIVE));

        $account = $this->createAccount(LoginTypeEnum::SPIS_CERT);
        self::assertSame('https://ws2c.datovka-test.gov.cz/cert/DS/arch', EndpointProvider::test()->getServiceLocation($account, ServiceTypeEnum::ARCHIVE));
        self::assertSame('https://ws2c.datovka.gov.cz/cert/DS/arch', EndpointProvider::production()->getServiceLocation($account, ServiceTypeEnum::ARCHIVE));

        $account = $this->createAccount(LoginTypeEnum::CERT_LOGIN_NAME_PASSWORD);
        self::assertSame('https://ws2c.datovka-test.gov.cz/certds/DS/arch', EndpointProvider::test()->getServiceLocation($account, ServiceTypeEnum::ARCHIVE));
        self::assertSame('https://ws2c.datovka.gov.cz/certds/DS/arch', EndpointProvider::production()->getServiceLocation($account, ServiceTypeEnum::ARCHIVE));

        $account = $this->createAccount(LoginTypeEnum::HOSTED_SPIS);
        self::assertSame('https://ws2c.datovka-test.gov.cz/hspis/DS/arch', EndpointProvider::test()->getServiceLocation($account, ServiceTypeEnum::ARCHIVE));
        self::assertSame('https://ws2c.datovka.gov.cz/hspis/DS/arch', EndpointProvider::production()->getServiceLocation($account, ServiceTypeEnum::ARCHIVE));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function validDomainProvider(): array
    {
        return [
            'production' => [EndpointProvider::PRODUCTION_DOMAIN],
            'test' => [EndpointProvider::TEST_DOMAIN],
            'kivs' => ['datovka.cms2.cz'],
            'legacy' => ['czebox.cz'],
            'single label' => ['isds'],
            'digits and dashes' => ['ds-2.internal-01.example.cz'],
        ];
    }

    #[DataProvider('validDomainProvider')]
    public function testValidDomainIsAccepted(string $domain): void
    {
        $account = $this->createAccount(LoginTypeEnum::NAME_PASSWORD);
        $endpointProvider = new EndpointProvider($domain);

        self::assertSame('https://ws1.' . $domain . '/DS/dx', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::INFO));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidDomainProvider(): array
    {
        return [
            'empty' => [''],
            'scheme' => ['https://evil.tld'],
            'scheme with path' => ['https://evil.tld/x'],
            'userinfo' => ['user@evil.tld'],
            'port' => ['evil.tld:8443'],
            'path' => ['evil.tld/../x'],
            'trailing slash' => ['evil.tld/'],
            'query' => ['evil.tld?a=b'],
            'header injection' => ["evil.tld\r\nX-Header: 1"],
            'null byte' => ["evil.tld\0"],
            'whitespace' => ['evil tld'],
            'leading dot' => ['.evil.tld'],
            'trailing dot' => ['evil.tld.'],
            'double dot' => ['evil..tld'],
            'leading dash' => ['-evil.tld'],
            'trailing dash' => ['evil-.tld'],
            'underscore' => ['evil_tld.cz'],
            'too long' => [str_repeat('a', 254)],
        ];
    }

    #[DataProvider('invalidDomainProvider')]
    public function testInvalidDomainIsRejected(string $domain): void
    {
        $this->expectException(InvalidEndpointDomain::class);

        new EndpointProvider($domain);
    }
}
