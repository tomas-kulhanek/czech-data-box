<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Response;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use TomasKulhanek\CzechDataBox\Entity\CreditRecord;
use TomasKulhanek\CzechDataBox\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;
use JMS\Serializer\Annotation as Serializer;

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
     * @var Collection<int, CreditRecord>
     * @Serializer\Type("ArrayCollection<TomasKulhanek\CzechDataBox\Entity\CreditRecord>")
     * @Serializer\XmlList(entry="ciRecord", inline=false, namespace="http://isds.czechpoint.cz/v20")
     * @Serializer\SerializedName("ciRecords")
     * @Serializer\XmlElement(cdata=false,namespace="http://isds.czechpoint.cz/v20")
     */
    protected Collection $records;

    public function __construct()
    {
        $this->records = new ArrayCollection();
    }

    public function getCurrentCredit(): ?int
    {
        return $this->currentCredit;
    }

    public function getNotifyEmail(): ?string
    {
        return $this->notifyEmail;
    }

    /**
     * @return Collection<int, CreditRecord>
     */
    public function getRecords(): Collection
    {
        return $this->records;
    }

}
