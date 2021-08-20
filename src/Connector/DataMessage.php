<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Connector;

use TomasKulhanek\CzechDataBox\Entity\File;
use TomasKulhanek\CzechDataBox\Exception\ConnectionException;
use TomasKulhanek\CzechDataBox\Exception\FileSizeOverflow;
use TomasKulhanek\CzechDataBox\Exception\MissingMainFile;
use TomasKulhanek\CzechDataBox\Exception\MissingRequiredField;
use TomasKulhanek\CzechDataBox\Exception\RecipientCountOverflow;
use TomasKulhanek\CzechDataBox\Exception\SystemExclusion;
use TomasKulhanek\CzechDataBox\Request;
use TomasKulhanek\CzechDataBox\Response;
use TomasKulhanek\CzechDataBox\Utils\BinarySuffix;
use SplFileInfo;

class DataMessage extends Connector
{

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
        return $this->send($account, self::OPERATIONS, $input, Response\AuthenticateMessage::class);
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
        return $this->send($account, self::INFO, $input, Response\VerifyMessage::class);
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
        $recipientsCount = $input->getRecipients()->count();
        if ($recipientsCount < 1) {
            throw new MissingRequiredField('recipient');
        }
        if ($recipientsCount > 50) {
            throw new RecipientCountOverflow(sprintf('More than 50 recipients are assigned. Currently, %d are added.', $recipientsCount));
        }
        $sumFileSize = 0;
        /** @var File $file */
        foreach ($input->getFiles() as $file) {
            if ($file->getEncodedContent() instanceof SplFileInfo) {
                $sumFileSize += $file->getEncodedContent()->getSize();
            }
        }
        if ($sumFileSize > 26214400) {
            throw new FileSizeOverflow(sprintf('Maximum size of all files can be maximal 25MB. Current size is %s.', BinarySuffix::convert($sumFileSize)));
        }
        if (!$input->getMainFile() instanceof File) {
            throw new MissingMainFile('The message can\'t be send without main attachment');
        }
        if (empty($input->getEnvelope()->getAnnotation())) {
            throw new MissingRequiredField('annotation');
        }
        return $this->send($account, self::OPERATIONS, $input, Response\CreateMessage::class);
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
        return $this->send($account, self::OPERATIONS, $input, Response\MessageDownload::class);
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
        return $this->send($account, self::OPERATIONS, $input, Response\SignedMessageDownload::class);
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
        return $this->send($account, self::OPERATIONS, $input, Response\SignedSentMessageDownload::class);
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
        return $this->send($account, self::OPERATIONS, $input, Response\ResignISDSDocument::class);
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
        return $this->send($account, self::INFO, $input, Response\MessageEnvelopeDownload::class);
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
        return $this->send($account, self::INFO, $input, Response\MarkMessageAsDownloaded::class);
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
        return $this->send($account, self::INFO, $input, Response\GetDeliveryInfo::class);
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
        return $this->send($account, self::INFO, $input, Response\GetSignedDeliveryInfo::class);
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
        return $this->send($account, self::INFO, $input, Response\GetListOfSentMessages::class);
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
        return $this->send($account, self::INFO, $input, Response\GetListOfReceivedMessages::class);
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
        return $this->send($account, self::INFO, $input, Response\GetMessageStateChanges::class);
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
        return $this->send($account, self::INFO, $input, Response\ConfirmDelivery::class);
    }

}
