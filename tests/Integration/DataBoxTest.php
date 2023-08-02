<?php

declare(strict_types=1);


namespace TomasKulhanek\Tests\CzechDataBox\Integration;

use PHPUnit\Framework\TestCase;

class DataBoxTest extends TestCase
{
	use AccountTrait;
	use ConnectorTrait;

	public function testOwnerInfoFromLogin(): void
	{
		$account = $this->createFOCertAccount();
		$response = $this->createGuzzleConnector()
			->getOwnerInfoFromLogin($account);

		self::assertTrue($response->getStatus()->isOk());
		self::assertSame($response->getOwnerInfo()->getLastName(), 'Datový');
		self::assertSame($response->getOwnerInfo()->getFirstName(), 'František');
		self::assertSame($response->getOwnerInfo()->getDataBoxId(), getenv('FO_ID_DS'));
		self::assertSame($response->getOwnerInfo()->getDataBoxType(), 'FO');
		self::assertSame($response->getOwnerInfo()->getIdentifier(), null);
		self::assertSame($response->getOwnerInfo()->getLastNameAtBirth(), null);
		self::assertSame($response->getOwnerInfo()->getFirmName(), null);
		self::assertSame($response->getOwnerInfo()->getBiCity(), 'Ostrava');
		self::assertSame($response->getOwnerInfo()->getBiCounty(), 'Ostrava-město');
		self::assertSame($response->getOwnerInfo()->getBiState(), 'CZ');
		self::assertSame($response->getOwnerInfo()->getNationality(), 'CZ');
		self::assertSame($response->getOwnerInfo()->getEmail(), null);
		self::assertSame($response->getOwnerInfo()->getTelNumber(), null);
		self::assertSame($response->getOwnerInfo()->getRegistryCode(), null);
		self::assertSame($response->getOwnerInfo()->getDataBoxState(), 1);
		self::assertFalse($response->getOwnerInfo()->getDataBoxEffectiveOvm());
		self::assertTrue($response->getOwnerInfo()->getDataBoxOpenAddressing());
		self::assertSame($response->getOwnerInfo()->getAdStreet(), 'Na Jízdárně');
	}
	
	public function testPasswordExpirationInfo(): void
	{
		$account = $this->createFOAccount();
		$response = $this->createGuzzleConnector()
			->getPasswordExpirationInfo($account);

		self::assertTrue($response->getStatus()->isOk());
		self::assertNull($response->getPasswordExpiry());
	}
}
