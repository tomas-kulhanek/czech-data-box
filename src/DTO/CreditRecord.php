<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class CreditRecord
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:ciRecord")
 */
class CreditRecord
{
    /**
     * @Serializer\Type("DateTimeImmutable<'Y-m-d\TH:i:s.uP','Europe/Prague'>")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:ciEventTime")
     */
    protected DateTimeImmutable $eventTime;

    /**
     * @Serializer\Type("int")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:ciEventType")
     */
    protected int $eventType;

    /**
     * @Serializer\Type("int")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:ciCreditChange")
     */
    protected int $creditChange;

    /**
     * @Serializer\Type("int")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:ciCreditAfter")
     */
    protected int $creditAfter;

    /**
     * @Serializer\SkipWhenEmpty
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:ciTransID")
     */
    protected ?string $transID = null;

    /**
     * @Serializer\SkipWhenEmpty
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:ciRecipientID")
     */
    protected ?string $recipientID = null;

    /**
     * @Serializer\SkipWhenEmpty
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:ciPDZID")
     */
    protected ?string $PDZID = null;

    /**
     * @Serializer\Type("int")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:ciNewCapacity")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?int $newCapacity = null;

    /**
     * @Serializer\Type("DateTimeImmutable<'Y-m-d'>")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:ciNewFrom")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?DateTimeImmutable $newFrom = null;

    /**
     * @Serializer\Type("DateTimeImmutable<'Y-m-d'>")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:ciNewTo")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?DateTimeImmutable $newTo = null;

    /**
     * @Serializer\Type("int")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:ciOldCapacity")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?int $oldCapacity = null;

    /**
     * @Serializer\Type("DateTimeImmutable<'Y-m-d'>")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:ciOldFrom")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?DateTimeImmutable $oldFrom = null;

    /**
     * @Serializer\Type("DateTimeImmutable<'Y-m-d'>")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:ciOldTo")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?DateTimeImmutable $oldTo = null;

    /**
     * @Serializer\Type("string")
     * @Serializer\SkipWhenEmpty
     * @Serializer\SerializedName("p:ciDoneBy")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?string $doneBy = null;
}
