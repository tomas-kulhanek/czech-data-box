<?php

declare(strict_types=1);


namespace TomasKulhanek\Tests\CzechDataBox\Integration;

use TomasKulhanek\CzechDataBox\Account;

trait AccountTrait
{
	protected function createPFOAccount(): Account
	{
		$account = new \TomasKulhanek\CzechDataBox\Account();
		$account->setProduction((bool) getenv('IS_PRODUCTION'));
		$account->setLoginType($account::LOGIN_NAME_PASSWORD);
		$account->setLoginName(getenv('PFO_LOGIN_USER'));
		$account->setPassword(getenv('PFO_PASSWORD_USER'));
		$account->setDataBoxId(getenv('PFO_ID_DS'));
		return $account;
	}

	protected function createPFOCertAccount(): Account
	{
		$account = new \TomasKulhanek\CzechDataBox\Account();
		$account->setProduction((bool) getenv('IS_PRODUCTION'));
		$account->setLoginType($account::LOGIN_HOSTED_SPIS);
		$account->setPublicKey(file_get_contents(__DIR__ . '/../../.data/cert.crt'));
		$account->setPrivateKey(file_get_contents(__DIR__ . '/../../.data/cert.pem'));
		$account->setLoginName(getenv('PFO_LOGIN_USER'));
		$account->setPassword(getenv('PFO_PASSWORD_USER'));
		$account->setPrivateKeyPassPhrase(getenv('CERT_PASSPHRASE'));
		$account->setDataBoxId(getenv('PFO_ID_DS'));
		return $account;
	}

	protected function createFOAccount(): Account
	{
		$account = new \TomasKulhanek\CzechDataBox\Account();
		$account->setProduction((bool) getenv('IS_PRODUCTION'));
		$account->setLoginType($account::LOGIN_NAME_PASSWORD);
		$account->setLoginName(getenv('FO_LOGIN_USER'));
		$account->setPassword(getenv('FO_PASSWORD_USER'));
		$account->setDataBoxId(getenv('FO_ID_DS'));
		return $account;
	}

	protected function createFOCertAccount(): Account
	{
		$account = new \TomasKulhanek\CzechDataBox\Account();
		$account->setProduction((bool) getenv('IS_PRODUCTION'));
		$account->setLoginType($account::LOGIN_HOSTED_SPIS);
		$account->setPublicKey(file_get_contents(__DIR__ . '/../../.data/cert.crt'));
		$account->setPrivateKey(file_get_contents(__DIR__ . '/../../.data/cert.pem'));
		$account->setLoginName(getenv('FO_LOGIN_USER'));
		$account->setPassword(getenv('FO_PASSWORD_USER'));
		$account->setPrivateKeyPassPhrase(getenv('CERT_PASSPHRASE'));
		$account->setDataBoxId(getenv('FO_ID_DS'));
		return $account;
	}

	protected function createOvmCertAccount(): Account
	{
		$account = new \TomasKulhanek\CzechDataBox\Account();
		$account->setProduction((bool) getenv('IS_PRODUCTION'));
		$account->setLoginType($account::LOGIN_HOSTED_SPIS);
		$account->setPublicKey(file_get_contents(__DIR__ . '/../../.data/cert.crt'));
		$account->setPrivateKey(file_get_contents(__DIR__ . '/../../.data/cert.pem'));
		$account->setLoginName(getenv('OVM_LOGIN_USER'));
		$account->setPassword(getenv('OVM_PASSWORD_USER'));
		$account->setPrivateKeyPassPhrase(getenv('CERT_PASSPHRASE'));
		$account->setDataBoxId(getenv('OVM_ID_DS'));
		return $account;
	}

	protected function createOVMAccount(): Account
	{
		$account = new \TomasKulhanek\CzechDataBox\Account();
		$account->setProduction((bool) getenv('IS_PRODUCTION'));
		$account->setLoginType($account::LOGIN_NAME_PASSWORD);
		$account->setLoginName(getenv('OVM_LOGIN_USER'));
		$account->setPassword(getenv('OVM_PASSWORD_USER'));
		$account->setDataBoxId(getenv('OVM_ID_DS'));
		return $account;
	}
}
