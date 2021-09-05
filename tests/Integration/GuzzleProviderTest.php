<?php

declare(strict_types=1);


namespace TomasKulhanek\Tests\CzechDataBox\Integration;

use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\Account;

class GuzzleProviderTest extends TestCase
{
    use AccountTrait;
    use ConnectorTrait;

    public function testLoginAndPassword()
    {
        $account = $this->createFOAccount();

        $response = $this->createGuzzleConnector()
            ->getOwnerInfoFromLogin($account);

        self::assertTrue($response->getStatus()->isOk());
    }

    public function testHspis()
    {
        $account = $this->createFOCertAccount();

        $response = $this->createGuzzleConnector()
            ->getOwnerInfoFromLogin($account);

        self::assertTrue($response->getStatus()->isOk());
    }

    public function testLoginAndPasswordAndCert()
    {
        $account = $this->createOvmCertAccount();
        $account->setLoginType(Account::LOGIN_CERT_LOGIN_NAME_PASSWORD);

        $response = $this->createGuzzleConnector()
            ->getOwnerInfoFromLogin($account);

        self::assertTrue($response->getStatus()->isOk());
    }

    public function testSpisCert()
    {
        $account = $this->createOvmCertAccount();
        $account->setLoginType(Account::LOGIN_SPIS_CERT);

        $response = $this->createGuzzleConnector()
            ->getOwnerInfoFromLogin($account);

        self::assertTrue($response->getStatus()->isOk());
    }
}
