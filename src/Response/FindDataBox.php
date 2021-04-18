<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Response;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use TomasKulhanek\CzechDataBox\Entity\OwnerInfo;
use TomasKulhanek\CzechDataBox\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class FindDataBox
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:FindDataBoxResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class FindDataBox extends IResponse
{

    use DataBoxStatus;

    /**
     * @var Collection<int, OwnerInfo>
     * @Serializer\Type("ArrayCollection<TomasKulhanek\CzechDataBox\Entity\OwnerInfo>")
     * @Serializer\XmlList(entry="dbOwnerInfo", inline=false,namespace="http://isds.czechpoint.cz/v20")
     * @Serializer\SerializedName("dbResults")
     * @Serializer\XmlElement(cdata=false,namespace="http://isds.czechpoint.cz/v20")
     */
    protected Collection $result;

    public function __construct()
    {
        $this->result = new ArrayCollection();
    }

    /**
     * @return Collection<int, OwnerInfo>
     */
    public function getResult(): Collection
    {
        return $this->result;
    }

}
