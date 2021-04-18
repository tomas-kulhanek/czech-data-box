<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Response;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use TomasKulhanek\CzechDataBox\Entity\DataBoxResult;
use TomasKulhanek\CzechDataBox\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class ISDSSearch3
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:ISDSSearch3Response", namespace="http://isds.czechpoint.cz/v20")
 */
class ISDSSearch3 extends IResponse
{

    use DataBoxStatus;

    /**
     * @Serializer\SkipWhenEmpty()
     * @Serializer\Type("int")
     * @Serializer\SerializedName("p:totalCount")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?int $totalCount = null;

    /**
     * @Serializer\SkipWhenEmpty()
     * @Serializer\Type("int")
     * @Serializer\SerializedName("p:currentCount")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?int $currentCount = null;

    /**
     * @Serializer\SkipWhenEmpty()
     * @Serializer\Type("int")
     * @Serializer\SerializedName("p:position")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?int $position = null;

    /**
     * @Serializer\SkipWhenEmpty()
     * @Serializer\Type("bool")
     * @Serializer\SerializedName("p:lastPage")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?bool $lastPage = null;

    /**
     * @var Collection<int, DataBoxResult>
     * @Serializer\Type("ArrayCollection<TomasKulhanek\CzechDataBox\Entity\DataBoxResult>")
     * @Serializer\XmlList(entry="dbResult", inline=false, namespace="http://isds.czechpoint.cz/v20")
     * @Serializer\SerializedName("dbResults")
     * @Serializer\XmlElement(cdata=false,namespace="http://isds.czechpoint.cz/v20")
     */
    protected Collection $result;

    public function __construct()
    {
        $this->result = new ArrayCollection();
    }

    public function getTotalCount(): ?int
    {
        return $this->totalCount;
    }

    public function setTotalCount(?int $totalCount): ISDSSearch3
    {
        $this->totalCount = $totalCount;
        return $this;
    }

    public function getCurrentCount(): ?int
    {
        return $this->currentCount;
    }

    public function setCurrentCount(?int $currentCount): ISDSSearch3
    {
        $this->currentCount = $currentCount;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): ISDSSearch3
    {
        $this->position = $position;
        return $this;
    }

    public function getLastPage(): ?bool
    {
        return $this->lastPage;
    }

    public function setLastPage(?bool $lastPage): ISDSSearch3
    {
        $this->lastPage = $lastPage;
        return $this;
    }

    /**
     * @return Collection<int, DataBoxResult>
     */
    public function getResult(): Collection
    {
        return $this->result;
    }

    /**
     * @param ArrayCollection<int, DataBoxResult> $result
     * @return ISDSSearch3
     */
    public function setResult(ArrayCollection $result): ISDSSearch3
    {
        $this->result = $result;
        return $this;
    }

}
