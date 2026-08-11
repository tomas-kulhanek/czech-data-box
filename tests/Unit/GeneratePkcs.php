<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use OpenSSLAsymmetricKey;
use OpenSSLCertificateSigningRequest;
use RuntimeException;

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
        if (!$privateKey instanceof OpenSSLAsymmetricKey) {
            throw new RuntimeException('Failed to generate a private key: ' . openssl_error_string());
        }
        $csr = openssl_csr_new($dn, $privateKey, ['digest_alg' => 'sha256']);
        if (!$csr instanceof OpenSSLCertificateSigningRequest) {
            throw new RuntimeException('Failed to generate a CSR: ' . openssl_error_string());
        }
        if (!$privateKey instanceof OpenSSLAsymmetricKey) {
            throw new RuntimeException('openssl_csr_new() replaced the private key with an unusable value.');
        }

        $x509 = openssl_csr_sign($csr, null, $privateKey, 365, ['digest_alg' => 'sha256']);
        if ($x509 === false) {
            throw new RuntimeException('Failed to sign the CSR: ' . openssl_error_string());
        }

        $certFilePath = __DIR__ . "/../_data/test.p12";
        if (!openssl_pkcs12_export_to_file($x509, $certFilePath, $privateKey, $passPhrase)) {
            throw new RuntimeException('Failed to export the PKCS12 bundle: ' . openssl_error_string());
        }
        $content = file_get_contents($certFilePath);
        if ($content === false) {
            throw new RuntimeException('Failed to read back ' . $certFilePath);
        }

        return $content;
    }
}
