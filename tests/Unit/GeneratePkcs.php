<?php

declare(strict_types=1);


namespace TomasKulhanek\Tests\CzechDataBox\Unit;

trait GeneratePkcs
{
	private function generateP12Certificate(string $passPhrase): string
	{
		$Info = [
			"countryName" => "CZ",
			"stateOrProvinceName" => "Prague",
			"localityName" => "Prague",
			"organizationName" => "Tomáš Kulhánek",
			"organizationalUnitName" => "Test Department",
			"commonName" => "Tester",
			"emailAddress" => "jsem+tests@tomaskulhanek.cz",
		];

		$Private_Key = null;
		$Unsigned_Cert = openssl_csr_new($Info, $Private_Key);

		$Signed_Cert = openssl_csr_sign($Unsigned_Cert, null, $Private_Key, 365);

		$certFilePath = __DIR__ . "/../_data/test.p12";
		openssl_pkcs12_export_to_file($Signed_Cert, $certFilePath, $Private_Key, $passPhrase);
		return file_get_contents($certFilePath);
	}
}
