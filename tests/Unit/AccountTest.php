<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\Account;

class AccountTest extends TestCase
{
    private const TEST_PASS_PHRASE = 'isds';

    public function testCertificateLogin(): void
    {
        $account = new Account();
        $account->setLoginType(Account::LOGIN_CERT_LOGIN_NAME_PASSWORD);
        self::assertTrue($account->usingCertificate());
        $account->setLoginType(Account::LOGIN_HOSTED_SPIS);
        self::assertTrue($account->usingCertificate());
        $account->setLoginType(Account::LOGIN_SPIS_CERT);
        self::assertTrue($account->usingCertificate());
        $account->setLoginType(Account::LOGIN_NAME_PASSWORD);
        self::assertFalse($account->usingCertificate());
    }

    public function testPkcs12Certificate(): void
    {
        $passPhrase = self::TEST_PASS_PHRASE;
        $pkcsContent = $this->generateP12Certificate($passPhrase);

        $cert_array = [];
        openssl_pkcs12_read($pkcsContent, $cert_array, $passPhrase);

        $account = new Account();
        $account->setPkcs12Certificate($pkcsContent, $passPhrase);

        self::assertSame($account->getPrivateKeyPassPhrase(), $passPhrase);
        self::assertSame($account->getPrivateKey(), $cert_array['pkey']);
        self::assertSame($account->getPublicKey(), $cert_array['cert']);
    }

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

        openssl_pkcs12_export_to_file($Signed_Cert, "test.p12", $Private_Key, $passPhrase);
        return file_get_contents(__DIR__ . "/../_data/test.p12");
    }
}
