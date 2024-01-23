<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

trait GeneratePkcs
{
    private function generateP12Certificate(string $passPhrase): string
    {
        $dn = [
            "countryName" => "CZ",
            "stateOrProvinceName" => "Prague",
            "localityName" => "Prague",
            "organizationName" => "Tomáš Kulhánek",
            "organizationalUnitName" => "Test Department",
            "commonName" => "Tester",
            "emailAddress" => "jsem+tests@tomaskulhanek.cz",
        ];

        $privateKey = openssl_pkey_new([
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ]);
        $csr = openssl_csr_new($dn, $privateKey, ['digest_alg' => 'sha256']);

        $x509 = openssl_csr_sign($csr, null, $privateKey, 365, ['digest_alg' => 'sha256']);

        $certFilePath = __DIR__ . "/../_data/test.p12";
        openssl_pkcs12_export_to_file($x509, $certFilePath, $privateKey, $passPhrase);
        return file_get_contents($certFilePath);
    }
}
