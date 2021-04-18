<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Response;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use TomasKulhanek\CzechDataBox\Entity\MessageRecord;
use TomasKulhanek\CzechDataBox\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class GetListOfReceivedMessages
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:GetListOfReceivedMessagesResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class GetListOfReceivedMessages extends IResponse
{

    use DataMessageStatus;

    /**
     * @var Collection<int, MessageRecord>
     * @Serializer\Type("ArrayCollection<TomasKulhanek\CzechDataBox\Entity\MessageRecord>")
     * @Serializer\XmlList(entry="dmRecord", inline=false, namespace="http://isds.czechpoint.cz/v20")
     * @Serializer\SerializedName("dmRecords")
     * @Serializer\XmlElement(cdata=false,namespace="http://isds.czechpoint.cz/v20")
     */
    protected Collection $record;

    public function __construct()
    {
        $this->record = new ArrayCollection();
    }

    /**
     * @return Collection<int, MessageRecord>
     */
    public function getRecord(): Collection
    {
        return $this->record;
    }

}
