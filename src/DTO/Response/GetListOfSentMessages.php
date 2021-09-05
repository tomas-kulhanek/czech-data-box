<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\MessageRecord;
use TomasKulhanek\CzechDataBox\DTO\Response\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

/**
 * Class GetListOfSentMessages
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:GetListOfSentMessagesResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class GetListOfSentMessages extends IResponse
{
    use DataMessageStatus;

    /**
     * @var MessageRecord[]
     * @Serializer\Type("array<TomasKulhanek\CzechDataBox\DTO\MessageRecord>")
     * @Serializer\XmlList(entry="dmRecord", inline=false, namespace="http://isds.czechpoint.cz/v20")
     * @Serializer\SerializedName("dmRecords")
     * @Serializer\XmlElement(cdata=false,namespace="http://isds.czechpoint.cz/v20")
     */
    protected array $records = [];

    /**
     * @return MessageRecord[]
     */
    public function getRecord(): array
    {
        return $this->records;
    }
}
