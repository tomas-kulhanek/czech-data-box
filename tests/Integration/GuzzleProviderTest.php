<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Integration;

use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;

class GuzzleProviderTest extends TestCase
{
    use AccountTrait;
    use ConnectorTrait;

    public function testLoginAndPassword(): void
    {
        $account = $this->createFOAccount();

        $response = $this->createGuzzleConnector()
            ->getOwnerInfoFromLogin($account);

        self::assertTrue($response->getStatus()->isOk());
    }

    public function testHspis(): void
    {
        $account = $this->createFOCertAccount();

        $response = $this->createGuzzleConnector()
            ->getOwnerInfoFromLogin($account);

        self::assertTrue($response->getStatus()->isOk());
    }

    public function testLoginAndPasswordAndCert(): void
    {
        $account = $this->createOvmCertAccount();
        $account->setLoginType(LoginTypeEnum::CERT_LOGIN_NAME_PASSWORD);

        $response = $this->createGuzzleConnector()
            ->getOwnerInfoFromLogin($account);

        self::assertTrue($response->getStatus()->isOk());
    }

    public function testSpisCert(): void
    {
        $account = $this->createOvmCertAccount();
        $account->setLoginType(LoginTypeEnum::SPIS_CERT);

        $response = $this->createGuzzleConnector()
            ->getOwnerInfoFromLogin($account);

        self::assertTrue($response->getStatus()->isOk());
    }
}
