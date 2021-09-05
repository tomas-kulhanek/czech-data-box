<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\PDZRecord;
use TomasKulhanek\CzechDataBox\DTO\Response\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

/**
 * Class PDZInfo
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:PDZInfoResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class PDZInfo extends IResponse
{
    use DataBoxStatus;

    /**
     * @var PDZRecord[]
     * @Serializer\Type("array<TomasKulhanek\CzechDataBox\DTO\PDZRecord>")
     * @Serializer\XmlList(entry="dbPDZRecord", inline=false)
     * @Serializer\SerializedName("p:dbPDZRecords")
     */
    protected array $pdzRecord = [];

    /**
     * @return PDZRecord[]
     */
    public function getPdzRecord(): array
    {
        return $this->pdzRecord;
    }

    /**
     * @param PDZRecord[] $pdzRecord
     * @return PDZInfo
     */
    public function setPdzRecord(array $pdzRecord): PDZInfo
    {
        $this->pdzRecord = $pdzRecord;
        return $this;
    }
}
