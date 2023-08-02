<?php

declare(strict_types=1);


namespace TomasKulhanek\Tests\CzechDataBox\Integration;

use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\Account;

class SymfonyProviderTest extends TestCase
{
	use AccountTrait;
	use ConnectorTrait;

	public function testLoginAndPassword(): void
	{
		$account = $this->createFOAccount();

		$response = $this->createSymfonyConnector()
			->getOwnerInfoFromLogin($account);

		self::assertTrue($response->getStatus()->isOk());
	}

	public function testHspis(): void
	{
		$account = $this->createFOCertAccount();

		$response = $this->createSymfonyConnector()
			->getOwnerInfoFromLogin($account);

		self::assertTrue($response->getStatus()->isOk());
	}

	public function testLoginAndPasswordAndCert(): void
	{
		$account = $this->createOvmCertAccount();
		$account->setLoginType(Account::LOGIN_CERT_LOGIN_NAME_PASSWORD);

		$response = $this->createSymfonyConnector()
			->getOwnerInfoFromLogin($account);

		self::assertTrue($response->getStatus()->isOk());
	}

	public function testSpisCert(): void
	{
		$account = $this->createOvmCertAccount();
		$account->setLoginType(Account::LOGIN_SPIS_CERT);

		$response = $this->createSymfonyConnector()
			->getOwnerInfoFromLogin($account);

		self::assertTrue($response->getStatus()->isOk());
	}
}
