<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Request;

use DateTimeImmutable;
use TomasKulhanek\CzechDataBox\IRequest;
use TomasKulhanek\CzechDataBox\Traits\DataBoxId;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class DataBoxCreditInfo
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:DataBoxCreditInfo",namespace="http://isds.czechpoint.cz/v20")
 * @Serializer\AccessorOrder("custom",custom={"dataBoxId","fromDate","toDate"})
 */
class DataBoxCreditInfo implements IRequest
{

    use DataBoxId;

    /**
     * @Serializer\SkipWhenEmpty
     * @Serializer\Type("DateTimeImmutable<'Y-m-d'>")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:ciFromDate")
     */
    private ?DateTimeImmutable $fromDate = null;

    /**
     * @Serializer\SkipWhenEmpty
     * @Serializer\Type("DateTimeImmutable<'Y-m-d'>")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:ciTodate")
     */
    private ?DateTimeImmutable $toDate = null;

    public function getFromDate(): ?DateTimeImmutable
    {
        return $this->fromDate;
    }

    public function setFromDate(?DateTimeImmutable $fromDate): DataBoxCreditInfo
    {
        $this->fromDate = $fromDate;
        return $this;
    }

    public function getToDate(): ?DateTimeImmutable
    {
        return $this->toDate;
    }

    public function setToDate(?DateTimeImmutable $toDate): DataBoxCreditInfo
    {
        $this->toDate = $toDate;
        return $this;
    }

}
