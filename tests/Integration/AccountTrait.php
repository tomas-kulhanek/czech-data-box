<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Integration;

use TomasKulhanek\CzechDataBox\Account;

trait AccountTrait
{
    protected function createPFOAccount(): Account
    {
        $account = new \TomasKulhanek\CzechDataBox\Account();
        $account->setProduction(false);
        $account->setLoginType($account::LOGIN_NAME_PASSWORD);
        $account->setLoginName((string) getenv('PFO_LOGIN_USER'));
        $account->setPassword((string) getenv('PFO_PASSWORD_USER'));
        $account->setDataBoxId((string) getenv('PFO_ID_DS'));
        return $account;
    }

    protected function createPFOCertAccount(): Account
    {
        $account = new \TomasKulhanek\CzechDataBox\Account();
        $account->setProduction(false);
        $account->setLoginType($account::LOGIN_HOSTED_SPIS);
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
        $account = new \TomasKulhanek\CzechDataBox\Account();
        $account->setProduction(false);
        $account->setLoginType($account::LOGIN_NAME_PASSWORD);
        $account->setLoginName((string) getenv('FO_LOGIN_USER'));
        $account->setPassword((string) getenv('FO_PASSWORD_USER'));
        $account->setDataBoxId((string) getenv('FO_ID_DS'));
        return $account;
    }

    protected function createFOCertAccount(): Account
    {
        $account = new \TomasKulhanek\CzechDataBox\Account();
        $account->setProduction(false);
        $account->setLoginType($account::LOGIN_HOSTED_SPIS);
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
        $account = new \TomasKulhanek\CzechDataBox\Account();
        $account->setProduction(false);
        $account->setLoginType($account::LOGIN_HOSTED_SPIS);
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
        $account = new \TomasKulhanek\CzechDataBox\Account();
        $account->setProduction(false);
        $account->setLoginType($account::LOGIN_NAME_PASSWORD);
        $account->setLoginName((string) getenv('OVM_LOGIN_USER'));
        $account->setPassword((string) getenv('OVM_PASSWORD_USER'));
        $account->setDataBoxId((string) getenv('OVM_ID_DS'));
        return $account;
    }
}
