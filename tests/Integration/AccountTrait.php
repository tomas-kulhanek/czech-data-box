<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Integration;

use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;

trait AccountTrait
{
    private const array REQUIRED_ENVIRONMENT_VARIABLES = [
        'FO_LOGIN_USER',
        'PFO_LOGIN_USER',
        'OVM_LOGIN_USER',
        'OVM_CERT_LOGIN_USER',
    ];

    private const string CERTIFICATE_PATH = __DIR__ . '/../../.data/cert.pem';

    public static function setUpBeforeClass(): void
    {
        foreach (self::REQUIRED_ENVIRONMENT_VARIABLES as $variable) {
            if (getenv($variable) === false || getenv($variable) === '') {
                self::markTestSkipped(
                    sprintf('Integration tests need ISDS credentials, %s is not set.', $variable)
                );
            }
        }

        if (!is_file(self::CERTIFICATE_PATH)) {
            self::markTestSkipped(
                sprintf('Integration tests need a client certificate in %s.', self::CERTIFICATE_PATH)
            );
        }
    }

    protected function createPFOAccount(): Account
    {
        $account = new Account();
        $account->setLoginType(LoginTypeEnum::NAME_PASSWORD);
        $account->setLoginName((string) getenv('PFO_LOGIN_USER'));
        $account->setPassword((string) getenv('PFO_PASSWORD_USER'));
        $account->setDataBoxId((string) getenv('PFO_ID_DS'));
        return $account;
    }

    protected function createPFOCertAccount(): Account
    {
        $account = new Account();
        $account->setLoginType(LoginTypeEnum::HOSTED_SPIS);
        $account->setPublicKey((string) file_get_contents(__DIR__ . '/../../.data/cert.crt'));
        $account->setPrivateKey((string) file_get_contents(__DIR__ . '/../../.data/cert.pem'));
        $account->setLoginName((string) getenv('PFO_LOGIN_USER'));
        $account->setPassword((string) getenv('PFO_PASSWORD_USER'));
        $account->setPrivateKeyPassPhrase((string) getenv('CERT_PASSPHRASE'));
        $account->setDataBoxId((string) getenv('PFO_ID_DS'));
        return $account;
    }

    protected function createFOAccount(): Account
    {
        $account = new Account();
        $account->setLoginType(LoginTypeEnum::NAME_PASSWORD);
        $account->setLoginName((string) getenv('FO_LOGIN_USER'));
        $account->setPassword((string) getenv('FO_PASSWORD_USER'));
        $account->setDataBoxId((string) getenv('FO_ID_DS'));
        return $account;
    }

    protected function createFOCertAccount(): Account
    {
        $account = new Account();
        $account->setLoginType(LoginTypeEnum::HOSTED_SPIS);
        $account->setPublicKey((string) file_get_contents(__DIR__ . '/../../.data/cert.crt'));
        $account->setPrivateKey((string) file_get_contents(__DIR__ . '/../../.data/cert.pem'));
        $account->setLoginName((string) getenv('FO_LOGIN_USER'));
        $account->setPassword((string) getenv('FO_PASSWORD_USER'));
        $account->setPrivateKeyPassPhrase((string) getenv('CERT_PASSPHRASE'));
        $account->setDataBoxId((string) getenv('FO_ID_DS'));
        return $account;
    }

    protected function createOvmCertAccount(): Account
    {
        $account = new Account();
        $account->setLoginType(LoginTypeEnum::HOSTED_SPIS);
        $account->setPublicKey((string) file_get_contents(__DIR__ . '/../../.data/cert.crt'));
        $account->setPrivateKey((string) file_get_contents(__DIR__ . '/../../.data/cert.pem'));
        $account->setLoginName((string) getenv('OVM_CERT_LOGIN_USER'));
        $account->setPassword((string) getenv('OVM_CERT_PASSWORD_USER'));
        $account->setPrivateKeyPassPhrase((string) getenv('CERT_PASSPHRASE'));
        $account->setDataBoxId((string) getenv('OVM_CERT_ID_DS'));
        return $account;
    }

    protected function createOVMAccount(): Account
    {
        $account = new Account();
        $account->setLoginType(LoginTypeEnum::NAME_PASSWORD);
        $account->setLoginName((string) getenv('OVM_LOGIN_USER'));
        $account->setPassword((string) getenv('OVM_PASSWORD_USER'));
        $account->setDataBoxId((string) getenv('OVM_ID_DS'));
        return $account;
    }
}
