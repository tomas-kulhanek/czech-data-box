<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Response;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use TomasKulhanek\CzechDataBox\Entity\PDZRecord;
use TomasKulhanek\CzechDataBox\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;
use JMS\Serializer\Annotation as Serializer;

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
     * @var Collection<int, PDZRecord>
     * @Serializer\Type("ArrayCollection<TomasKulhanek\CzechDataBox\Entity\PDZRecord>")
     * @Serializer\XmlList(entry="dbPDZRecord", inline=false)
     * @Serializer\SerializedName("p:dbPDZRecords")
     */
    protected Collection $pdzRecord;

    public function __construct()
    {
        $this->pdzRecord = new ArrayCollection();
    }

    /**
     * @return Collection<int, PDZRecord>
     */
    public function getPdzRecord(): Collection
    {
        return $this->pdzRecord;
    }

    /**
     * @param Collection<int, PDZRecord> $pdzRecord
     * @return PDZInfo
     */
    public function setPdzRecord(Collection $pdzRecord): PDZInfo
    {
        $this->pdzRecord = $pdzRecord;
        return $this;
    }

}
