<?php

declare(strict_types=1);


namespace TomasKulhanek\Tests\CzechDataBox\Integration;

use TomasKulhanek\CzechDataBox\Account;

trait AccountTrait
{
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
