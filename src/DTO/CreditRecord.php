<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'ciRecord')]
class CreditRecord
{
    #[Serializer\Type('DateTimeImmutable<\'Y-m-d\TH:i:s.uP\',\'Europe/Prague\'>')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('ciEventTime')]
    protected DateTimeImmutable $eventTime;

    #[Serializer\Type('int')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName("p:ciEventType")]
    #[Assert\Positive()]
    protected int $eventType;

    #[Serializer\Type("int")]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName("p:ciCreditChange")]
    #[Assert\Type('integer')]
    protected int $creditChange;

    #[Serializer\Type("int")]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName("p:ciCreditAfter")]
    #[Assert\Type('integer')]
    protected int $creditAfter;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type("string")]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName("p:ciTransID")]
    protected ?string $transID = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type("string")]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName("p:ciRecipientID")]
    protected ?string $recipientID = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type("string")]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName("p:ciPDZID")]
    protected ?string $PDZID = null;

    #[Serializer\Type("int")]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName("p:ciNewCapacity")]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?int $newCapacity = null;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName("p:ciNewFrom")]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?DateTimeImmutable $newFrom = null;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName("p:ciNewTo")]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?DateTimeImmutable $newTo = null;

    #[Serializer\Type("int")]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName("p:ciOldCapacity")]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?int $oldCapacity = null;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName("p:ciOldFrom")]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?DateTimeImmutable $oldFrom = null;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName("p:ciOldTo")]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?DateTimeImmutable $oldTo = null;

    #[Serializer\Type("string")]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName("p:ciDoneBy")]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?string $doneBy = null;
}
