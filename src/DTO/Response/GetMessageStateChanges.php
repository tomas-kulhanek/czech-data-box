<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\StateChangeRecord;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

/**
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:GetMessageStateChangesResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class GetMessageStateChanges extends IResponse
{
    use DataMessageStatus;

    /**
     * @var StateChangeRecord[]
     * @Serializer\Type("array<TomasKulhanek\CzechDataBox\DTO\StateChangeRecord>")
     * @Serializer\XmlList(entry="dmRecord", inline=false)
     * @Serializer\SerializedName("p:dmRecords")
     */
    protected array $record = [];

    /**
     * @return StateChangeRecord[]
     */
    public function getRecords(): array
    {
        return $this->record;
    }
}
