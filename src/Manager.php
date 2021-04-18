<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox;

use TomasKulhanek\CzechDataBox\Connector\Account;
use TomasKulhanek\CzechDataBox\Connector\DataBox;
use TomasKulhanek\CzechDataBox\Connector\DataMessage;
use TomasKulhanek\CzechDataBox\Connector\SearchDataBox;
use TomasKulhanek\CzechDataBox\Exception\ConnectionException;
use TomasKulhanek\CzechDataBox\Exception\FileSizeOverflow;
use TomasKulhanek\CzechDataBox\Exception\MissingMainFile;
use TomasKulhanek\CzechDataBox\Exception\MissingRequiredField;
use TomasKulhanek\CzechDataBox\Exception\RecipientCountOverflow;
use TomasKulhanek\CzechDataBox\Exception\SystemExclusion;

class Manager
{

    private DataBox $dataBox;

    private DataMessage $dataMessage;

    private SearchDataBox $searchData;

    public function __construct(DataBox $dataBox, DataMessage $dataMessage, SearchDataBox $searchDataBox)
    {
        $this->dataBox = $dataBox;
        $this->dataMessage = $dataMessage;
        $this->searchData = $searchDataBox;
    }

    public function getOwnerInfoFromLogin(Account $account): Response\GetOwnerInfoFromLogin
    {
        return $this->dataBox->getOwnerInfoFromLogin($account);
    }

    public function changeIsdsPassword(Account $account, Request\ChangeISDSPassword $input): Response\ChangeISDSPassword
    {
        return $this->dataBox->changeIsdsPassword($account, $input);
    }

    public function getPasswordExpirationInfo(Account $account): Response\GetPasswordInfo
    {
        return $this->dataBox->getPasswordExpirationInfo($account);
    }

    /**
     * Ověření platnosti uložené datové zprávy
     *
     * @param Account $account
     * @param Request\AuthenticateMessage $input
     * @return Response\AuthenticateMessage
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function authenticateMessage(Account $account, Request\AuthenticateMessage $input): Response\AuthenticateMessage
    {
        return $this->dataMessage->authenticateMessage($account, $input);
    }

    /**
     * Ověření kopie uložené zprávy proti originálu v ISDS
     *
     * @param Account $account
     * @param Request\VerifyMessage $input
     * @return Response\VerifyMessage
     * @throws ConnectionException
     * @throws SystemExclusion
     * @deprecated
     */
    public function verifyMessage(Account $account, Request\VerifyMessage $input): Response\VerifyMessage
    {
        return $this->dataMessage->verifyMessage($account, $input);
    }


    /**
     * Vytvoření a odeslání nové zprávy pro více adresátů
     *
     * @param Account $account
     * @param Request\CreateMessage $input
     * @return Response\CreateMessage
     * @throws FileSizeOverflow
     * @throws MissingMainFile
     * @throws MissingRequiredField
     * @throws RecipientCountOverflow
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function createMessage(Account $account, Request\CreateMessage $input): Response\CreateMessage
    {
        return $this->dataMessage->createMessage($account, $input);
    }

    /**
     * Stažení došlé zprávy
     *
     * @param Account $account
     * @param Request\MessageDownload $input
     * @return Response\MessageDownload
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function messageDownload(Account $account, Request\MessageDownload $input): Response\MessageDownload
    {
        return $this->dataMessage->messageDownload($account, $input);
    }

    /**
     * Stažení došlé zprávy s podpisem značkou MV
     *
     * @param Account $account
     * @param Request\SignedMessageDownload $input
     * @return Response\SignedMessageDownload
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function signedMessageDownload(Account $account, Request\SignedMessageDownload $input): Response\SignedMessageDownload
    {
        return $this->dataMessage->signedMessageDownload($account, $input);
    }

    /**
     * Stažení odeslané zprávy s podpisem MV
     *
     * @param Account $account
     * @param Request\SignedSentMessageDownload $input
     * @return Response\SignedSentMessageDownload
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function signedSentMessageDownload(Account $account, Request\SignedSentMessageDownload $input): Response\SignedSentMessageDownload
    {
        return $this->dataMessage->signedSentMessageDownload($account, $input);
    }


    /**
     * Přepodepsání zprávy, dodejky či doručenky
     *
     * @param Account $account
     * @param Request\ResignISDSDocument $input
     * @return Response\ResignISDSDocument
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function resignIsdsDocument(Account $account, Request\ResignISDSDocument $input): Response\ResignISDSDocument
    {
        return $this->dataMessage->resignIsdsDocument($account, $input);
    }

    /**
     * Stažení obálky došlé zprávy
     *
     * @param Account $account
     * @param Request\MessageEnvelopeDownload $input
     * @return Response\MessageEnvelopeDownload
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function messageEnvelopeDownload(Account $account, Request\MessageEnvelopeDownload $input): Response\MessageEnvelopeDownload
    {
        return $this->dataMessage->messageEnvelopeDownload($account, $input);
    }

    /**
     * Označení zprávy jako „Přečtená“
     *
     * @param Account $account
     * @param Request\MarkMessageAsDownloaded $input
     * @return Response\MarkMessageAsDownloaded
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function markMessageAsDownloaded(Account $account, Request\MarkMessageAsDownloaded $input): Response\MarkMessageAsDownloaded
    {
        return $this->dataMessage->markMessageAsDownloaded($account, $input);
    }

    /**
     * Stažení informace o dodání a doručování zprávy
     *
     * @param Account $account
     * @param Request\GetDeliveryInfo $input
     * @return Response\GetDeliveryInfo
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function getDeliveryInfo(Account $account, Request\GetDeliveryInfo $input): Response\GetDeliveryInfo
    {
        return $this->dataMessage->getDeliveryInfo($account, $input);
    }

    /**
     * Stažení informace o dodání a doručování zprávy, s podpisem značkou MV
     *
     * @param Account $account
     * @param Request\GetSignedDeliveryInfo $input
     * @return Response\GetSignedDeliveryInfo
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function getSignedDeliveryInfo(Account $account, Request\GetSignedDeliveryInfo $input): Response\GetSignedDeliveryInfo
    {
        return $this->dataMessage->getSignedDeliveryInfo($account, $input);
    }


    /**
     * Stazeni seznamu odeslanych zprav urceneho casovym intervalem, organizacni jednotkou odesilatele,
     * filtrem na stav zprav a usekem poradovych cisel zaznamu. Vrati seznam zprav.
     *
     * @param Account $account
     * @param Request\GetListOfSentMessages $input
     * @return Response\GetListOfSentMessages
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function getListOfSentMessages(Account $account, Request\GetListOfSentMessages $input): Response\GetListOfSentMessages
    {
        return $this->dataMessage->getListOfSentMessages($account, $input);
    }

    /**
     * Stazeni seznamu doslych zprav urceneho casovym intervalem,
     * zpresnenim organizacni jednotky adresata (pouze ESS), filtrem na stav zprav
     * a usekem poradovych cisel zaznamu
     *
     * @param Account $account
     * @param Request\GetListOfReceivedMessages $input
     * @return Response\GetListOfReceivedMessages
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function getListOfReceivedMessages(Account $account, Request\GetListOfReceivedMessages $input): Response\GetListOfReceivedMessages
    {
        return $this->dataMessage->getListOfReceivedMessages($account, $input);
    }

    /**
     * Stažení seznamu odeslaných zpráv, u nichž došlo ke změně stavu
     *
     * @param Account $account
     * @param Request\GetMessageStateChanges $input
     * @return Response\GetMessageStateChanges
     * @throws ConnectionException
     * @throws SystemExclusion
     */
    public function getMessageStateChanges(Account $account, Request\GetMessageStateChanges $input): Response\GetMessageStateChanges
    {
        return $this->dataMessage->getMessageStateChanges($account, $input);
    }

    /**
     * Potvrzeni doruceni komercni zpravy
     *
     * @param Account $account
     * @param Request\ConfirmDelivery $input
     * @return Response\ConfirmDelivery
     * @throws ConnectionException
     * @throws SystemExclusion
     * @deprecated
     */
    public function confirmDelivery(Account $account, Request\ConfirmDelivery $input): Response\ConfirmDelivery
    {
        return $this->dataMessage->confirmDelivery($account, $input);
    }

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
        return $this->searchData->findDataBox($account, $input);
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
        return $this->searchData->pdzInfo($account, $input);
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
        return $this->searchData->dataBoxCreditInfo($account, $input);
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
        return $this->searchData->isdsSearch3($account, $input);
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
        return $this->searchData->getDataBoxActivityStatus($account, $input);
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
        return $this->searchData->dtInfo($account, $input);
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
        return $this->searchData->pdzSendInfo($account, $input);
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
        return $this->searchData->findPersonalDataBox($account, $input);
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
        return $this->searchData->getDataBoxList($account, $input);
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
        return $this->searchData->checkDataBox($account, $input);
    }

}
