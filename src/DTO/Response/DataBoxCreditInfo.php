<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\CreditRecord;
use TomasKulhanek\CzechDataBox\DTO\Response\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

/**
 * Class DataBoxCreditInfo
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:DataBoxCreditInfoResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class DataBoxCreditInfo extends IResponse
{
    use DataBoxStatus;

    /**
     * @Serializer\Type("int")
     * @Serializer\SkipWhenEmpty
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:currentCredit")
     */
    protected ?int $currentCredit = null;

    /**
     * @Serializer\Type("string")
     * @Serializer\SkipWhenEmpty
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:notifEmail")
     */
    protected ?string $notifyEmail = null;

    /**
     * @var CreditRecord[]
     * @Serializer\Type("array<TomasKulhanek\CzechDataBox\DTO\CreditRecord>")
     * @Serializer\XmlList(entry="ciRecord", inline=false, namespace="http://isds.czechpoint.cz/v20")
     * @Serializer\SerializedName("ciRecords")
     * @Serializer\XmlElement(cdata=false,namespace="http://isds.czechpoint.cz/v20")
     */
    protected array $records = [];

    public function getCurrentCredit(): ?int
    {
        return $this->currentCredit;
    }

    public function getNotifyEmail(): ?string
    {
        return $this->notifyEmail;
    }

    /**
     * @return CreditRecord[]
     */
    public function getRecords(): array
    {
        return $this->records;
    }
}
