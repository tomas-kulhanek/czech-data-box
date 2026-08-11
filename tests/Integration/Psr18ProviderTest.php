<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Integration;

use GuzzleHttp\Client;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Connector;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Provider\EndpointProvider;
use TomasKulhanek\CzechDataBox\Provider\Psr18ClientProvider;
use TomasKulhanek\Tests\CzechDataBox\SerializerTrait;

class Psr18ProviderTest extends TestCase
{
    use AccountTrait;
    use SerializerTrait;

    private function createPsr18Connector(?Client $client = null): Connector
    {
        $factory = new Psr17Factory();
        $provider = new Psr18ClientProvider(
            $client ?? new Client(),
            $factory,
            $factory,
            EndpointProvider::test()
        );

        return new Connector(self::createSerializer(), $provider);
    }

    private function createMutualTlsClient(): Client
    {
        $passPhrase = (string) getenv('CERT_PASSPHRASE');

        return new Client([
            'cert' => [__DIR__ . '/../../.data/cert.crt', $passPhrase],
            'ssl_key' => [__DIR__ . '/../../.data/cert.pem', $passPhrase],
        ]);
    }

    private function withoutAccountCertificate(Account $account): Account
    {
        $stripped = new Account();
        $stripped->setLoginType($account->getLoginType());
        $stripped->setDataBoxId($account->getDataBoxId());
        if ($account->getLoginName() !== null) {
            $stripped->setLoginName($account->getLoginName());
        }
        if ($account->getPassword() !== null) {
            $stripped->setPassword($account->getPassword());
        }

        return $stripped;
    }

    public function testLoginAndPassword(): void
    {
        $account = $this->createFOAccount();

        $response = $this->createPsr18Connector()
            ->getOwnerInfoFromLogin2($account);

        self::assertTrue($response->getStatus()->isOk());
    }

    public function testHspis(): void
    {
        $account = $this->withoutAccountCertificate($this->createFOCertAccount());

        $response = $this->createPsr18Connector($this->createMutualTlsClient())
            ->getOwnerInfoFromLogin2($account);

        self::assertTrue($response->getStatus()->isOk());
    }

    public function testLoginAndPasswordAndCert(): void
    {
        $account = $this->withoutAccountCertificate($this->createOvmCertAccount());
        $account->setLoginType(LoginTypeEnum::CERT_LOGIN_NAME_PASSWORD);

        $response = $this->createPsr18Connector($this->createMutualTlsClient())
            ->getOwnerInfoFromLogin2($account);

        self::assertTrue($response->getStatus()->isOk());
    }

    public function testSpisCert(): void
    {
        $account = $this->withoutAccountCertificate($this->createOvmCertAccount());
        $account->setLoginType(LoginTypeEnum::SPIS_CERT);

        $response = $this->createPsr18Connector($this->createMutualTlsClient())
            ->getOwnerInfoFromLogin2($account);

        self::assertTrue($response->getStatus()->isOk());
    }
}
