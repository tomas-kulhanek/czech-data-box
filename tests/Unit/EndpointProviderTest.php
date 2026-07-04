<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;
use TomasKulhanek\CzechDataBox\Provider\EndpointProvider;

class EndpointProviderTest extends TestCase
{
    public function testInfoServices(): void
    {
        $account = new Account();
        $account->setProduction(false);
        $account->setLoginType(LoginTypeEnum::NAME_PASSWORD);

        $endpointProvider = new EndpointProvider();
        self::assertSame('https://ws1.datovka-test.gov.cz/DS/dx', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::INFO));
        $account->setProduction(true);
        self::assertSame('https://ws1.datovka.gov.cz/DS/dx', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::INFO));

        $account->setProduction(false);
        $account->setLoginType(LoginTypeEnum::SPIS_CERT);
        self::assertSame('https://ws1c.datovka-test.gov.cz/cert/DS/dx', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::INFO));
        $account->setProduction(true);
        self::assertSame('https://ws1c.datovka.gov.cz/cert/DS/dx', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::INFO));

        $account->setLoginType(LoginTypeEnum::CERT_LOGIN_NAME_PASSWORD);
        $account->setProduction(false);
        self::assertSame('https://ws1c.datovka-test.gov.cz/certds/DS/dx', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::INFO));
        $account->setProduction(true);
        self::assertSame('https://ws1c.datovka.gov.cz/certds/DS/dx', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::INFO));
    }

    public function testSupplementaryServices(): void
    {
        $account = new Account();
        $account->setProduction(false);
        $account->setLoginType(LoginTypeEnum::NAME_PASSWORD);

        $endpointProvider = new EndpointProvider();
        self::assertSame('https://ws1.datovka-test.gov.cz/DS/DsManage', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::SUPPLEMENTARY));
        $account->setProduction(true);
        self::assertSame('https://ws1.datovka.gov.cz/DS/DsManage', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::SUPPLEMENTARY));

        $account->setProduction(false);
        $account->setLoginType(LoginTypeEnum::SPIS_CERT);
        self::assertSame('https://ws1c.datovka-test.gov.cz/cert/DS/DsManage', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::SUPPLEMENTARY));
        $account->setProduction(true);
        self::assertSame('https://ws1c.datovka.gov.cz/cert/DS/DsManage', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::SUPPLEMENTARY));

        $account->setLoginType(LoginTypeEnum::CERT_LOGIN_NAME_PASSWORD);
        $account->setProduction(false);
        self::assertSame('https://ws1c.datovka-test.gov.cz/certds/DS/DsManage', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::SUPPLEMENTARY));
        $account->setProduction(true);
        self::assertSame('https://ws1c.datovka.gov.cz/certds/DS/DsManage', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::SUPPLEMENTARY));
    }

    public function testAccessServices(): void
    {
        $account = new Account();
        $account->setProduction(false);
        $account->setLoginType(LoginTypeEnum::NAME_PASSWORD);

        $endpointProvider = new EndpointProvider();
        self::assertSame('https://ws1.datovka-test.gov.cz/DS/DsManage', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::ACCESS));
        $account->setProduction(true);
        self::assertSame('https://ws1.datovka.gov.cz/DS/DsManage', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::ACCESS));

        $account->setProduction(false);
        $account->setLoginType(LoginTypeEnum::SPIS_CERT);
        self::assertSame('https://ws1c.datovka-test.gov.cz/cert/DS/DsManage', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::ACCESS));
        $account->setProduction(true);
        self::assertSame('https://ws1c.datovka.gov.cz/cert/DS/DsManage', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::ACCESS));

        $account->setLoginType(LoginTypeEnum::CERT_LOGIN_NAME_PASSWORD);
        $account->setProduction(false);
        self::assertSame('https://ws1c.datovka-test.gov.cz/certds/DS/DsManage', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::ACCESS));
        $account->setProduction(true);
        self::assertSame('https://ws1c.datovka.gov.cz/certds/DS/DsManage', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::ACCESS));
    }

    public function testSearchServices(): void
    {
        $account = new Account();
        $account->setProduction(false);
        $account->setLoginType(LoginTypeEnum::NAME_PASSWORD);

        $endpointProvider = new EndpointProvider();
        self::assertSame('https://ws1.datovka-test.gov.cz/DS/df', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::SEARCH));
        $account->setProduction(true);
        self::assertSame('https://ws1.datovka.gov.cz/DS/df', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::SEARCH));

        $account->setProduction(false);
        $account->setLoginType(LoginTypeEnum::SPIS_CERT);
        self::assertSame('https://ws1c.datovka-test.gov.cz/cert/DS/df', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::SEARCH));
        $account->setProduction(true);
        self::assertSame('https://ws1c.datovka.gov.cz/cert/DS/df', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::SEARCH));

        $account->setLoginType(LoginTypeEnum::CERT_LOGIN_NAME_PASSWORD);
        $account->setProduction(false);
        self::assertSame('https://ws1c.datovka-test.gov.cz/certds/DS/df', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::SEARCH));
        $account->setProduction(true);
        self::assertSame('https://ws1c.datovka.gov.cz/certds/DS/df', $endpointProvider->getServiceLocation($account, ServiceTypeEnum::SEARCH));
    }
}
