<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Connector;

use TomasKulhanek\CzechDataBox\Exception\ConnectionException;
use TomasKulhanek\CzechDataBox\Exception\SystemExclusion;
use TomasKulhanek\CzechDataBox\Request;
use TomasKulhanek\CzechDataBox\Response;

class SearchDataBox extends Connector
{

    /**
     * Vyhledani datove schranky
     *
     * @param Account $account
     * @param Request\FindDataBox $input
     * @return Response\FindDataBox
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function findDataBox(Account $account, Request\FindDataBox $input): Response\FindDataBox
    {
        return $this->send($account, self::SEARCH, $input, Response\FindDataBox::class);
    }

    /**
     * todo check
     *
     * @param Account $account
     * @param Request\PDZInfo $input
     * @return Response\PDZInfo
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function pdzInfo(Account $account, Request\PDZInfo $input): Response\PDZInfo
    {
        return $this->send($account, self::SEARCH, $input, Response\PDZInfo::class);
    }

    /**
     * todo check ciRecord
     *
     * @param Account $account
     * @param Request\DataBoxCreditInfo $input
     * @return Response\DataBoxCreditInfo
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function dataBoxCreditInfo(Account $account, Request\DataBoxCreditInfo $input): Response\DataBoxCreditInfo
    {
        return $this->send($account, self::SEARCH, $input, Response\DataBoxCreditInfo::class);
    }

    /**
     * fulltextove vyhledavani
     *
     * @param Account $account
     * @param Request\ISDSSearch3 $input
     * @return Response\ISDSSearch3
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function isdsSearch3(Account $account, Request\ISDSSearch3 $input): Response\ISDSSearch3
    {
        return $this->send($account, self::SEARCH, $input, Response\ISDSSearch3::class);
    }

    /**
     * @param Account $account
     * @param Request\GetDataBoxActivityStatus $input
     * @return Response\GetDataBoxActivityStatus
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function getDataBoxActivityStatus(Account $account, Request\GetDataBoxActivityStatus $input): Response\GetDataBoxActivityStatus
    {
        return $this->send($account, self::SEARCH, $input, Response\GetDataBoxActivityStatus::class);
    }

    /**
     * @param Account $account
     * @param Request\DTInfo $input
     * @return Response\DTInfo
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function dtInfo(Account $account, Request\DTInfo $input): Response\DTInfo
    {
        return $this->send($account, self::SEARCH, $input, Response\DTInfo::class);
    }

    /**
     * @param Account $account
     * @param Request\PDZSendInfo $input
     * @return Response\PDZSendInfo
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function pdzSendInfo(Account $account, Request\PDZSendInfo $input): Response\PDZSendInfo
    {
        return $this->send($account, self::SEARCH, $input, Response\PDZSendInfo::class);
    }

    /**
     * Vyhledavani osobnich schranek. Jde pouzit jen pro OVM
     *
     * @param Account $account
     * @param Request\FindPersonalDataBox $input
     * @return Response\FindPersonalDataBox
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function findPersonalDataBox(Account $account, Request\FindPersonalDataBox $input): Response\FindPersonalDataBox
    {
        return $this->send($account, self::SEARCH, $input, Response\FindPersonalDataBox::class);
    }

    /**
     * @param Account $account
     * @param Request\GetDataBoxList $input
     * @return Response\GetDataBoxList
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function getDataBoxList(Account $account, Request\GetDataBoxList $input): Response\GetDataBoxList
    {
        return $this->send($account, self::SEARCH, $input, Response\GetDataBoxList::class);
    }

    /**
     * Kontrola, zda datova schranka je aktivni
     *
     * @param Account $account
     * @param Request\CheckDataBox $input
     * @return Response\CheckDataBox
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function checkDataBox(Account $account, Request\CheckDataBox $input): Response\CheckDataBox
    {
        return $this->send($account, self::SEARCH, $input, Response\CheckDataBox::class);
    }

}
