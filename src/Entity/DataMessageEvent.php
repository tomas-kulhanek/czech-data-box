<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Entity;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class DataMessageEvent
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:dmEvent")
 */
class DataMessageEvent
{

    /**
     * @Serializer\Type("DateTimeImmutable<'Y-m-d\TH:i:s.uP','Europe/Prague'>")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dnEventTime")
     */
    protected DateTimeImmutable $time;

    /**
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dmEventDescr")
     */
    protected string $description;

    public function getTime(): DateTimeImmutable
    {
        return $this->time;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

}
