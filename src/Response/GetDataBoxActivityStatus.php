<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Response;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use TomasKulhanek\CzechDataBox\Entity\Period;
use TomasKulhanek\CzechDataBox\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataBoxId;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class GetDataBoxActivityStatus
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:GetDataBoxActivityStatusResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class GetDataBoxActivityStatus extends IResponse
{

    use DataBoxStatus;
    use DataBoxId;

    /**
     * @var Collection<int, Period>
     * @Serializer\Type("ArrayCollection<TomasKulhanek\CzechDataBox\Entity\Period>")
     * @Serializer\XmlList(entry="Period", inline=false, namespace="http://isds.czechpoint.cz/v20")
     * @Serializer\SerializedName("Periods")
     * @Serializer\XmlElement(cdata=false,namespace="http://isds.czechpoint.cz/v20")
     */
    protected $period;

    public function __construct()
    {
        $this->period = new ArrayCollection();
    }

    /**
     * @return Collection<int, Period>
     */
    public function getPeriod(): Collection
    {
        return $this->period;
    }

}
