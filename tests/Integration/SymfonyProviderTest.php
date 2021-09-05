<?php

declare(strict_types=1);


namespace TomasKulhanek\Tests\CzechDataBox\Integration;

use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\Account;

class SymfonyProviderTest extends TestCase
{
    use AccountTrait;
    use ConnectorTrait;

    public function testLoginAndPassword()
    {
        $account = $this->createFOAccount();

        $response = $this->createSymfonyConnector()
            ->getOwnerInfoFromLogin($account);

        self::assertTrue($response->getStatus()->isOk());
    }

    public function testHspis()
    {
        $account = $this->createFOCertAccount();

        $response = $this->createSymfonyConnector()
            ->getOwnerInfoFromLogin($account);

        self::assertTrue($response->getStatus()->isOk());
    }

    public function testLoginAndPasswordAndCert()
    {
        $account = $this->createOvmCertAccount();
        $account->setLoginType(Account::LOGIN_CERT_LOGIN_NAME_PASSWORD);

        $response = $this->createSymfonyConnector()
            ->getOwnerInfoFromLogin($account);

        self::assertTrue($response->getStatus()->isOk());
    }

    public function testSpisCert()
    {
        $account = $this->createOvmCertAccount();
        $account->setLoginType(Account::LOGIN_SPIS_CERT);

        $response = $this->createSymfonyConnector()
            ->getOwnerInfoFromLogin($account);

        self::assertTrue($response->getStatus()->isOk());
    }
}
