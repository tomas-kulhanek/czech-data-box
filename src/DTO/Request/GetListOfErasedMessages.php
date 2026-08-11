<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'GetListOfErasedMessages')]
class GetListOfErasedMessages implements Request
{
    public const MESSAGE_TYPE_SENT = 'SENT';
    public const MESSAGE_TYPE_RECEIVED = 'RECEIVED';
    public const OUT_FORMAT_XML = 'XML';
    public const OUT_FORMAT_CSV = 'CSV';

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\SerializedName('dmFromDate')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?DateTimeImmutable $fromDate = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Serializer\SerializedName('dmToDate')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?DateTimeImmutable $toDate = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\SerializedName('dmYear')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?int $year = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\SerializedName('dmMonth')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?int $month = null;

    /**
     * Allowed values: SENT, RECEIVED.
     */
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmMessageType')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected string $messageType;

    /**
     * Allowed values: XML, CSV.
     */
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmOutFormat')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected string $outFormat;

    public function getFromDate(): ?DateTimeImmutable
    {
        return $this->fromDate;
    }

    public function setFromDate(?DateTimeImmutable $fromDate): GetListOfErasedMessages
    {
        $this->fromDate = $fromDate;
        return $this;
    }

    public function getToDate(): ?DateTimeImmutable
    {
        return $this->toDate;
    }

    public function setToDate(?DateTimeImmutable $toDate): GetListOfErasedMessages
    {
        $this->toDate = $toDate;
        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): GetListOfErasedMessages
    {
        $this->year = $year;
        return $this;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(?int $month): GetListOfErasedMessages
    {
        $this->month = $month;
        return $this;
    }

    public function getMessageType(): string
    {
        return $this->messageType;
    }

    public function setMessageType(string $messageType): GetListOfErasedMessages
    {
        $this->messageType = $messageType;
        return $this;
    }

    public function getOutFormat(): string
    {
        return $this->outFormat;
    }

    public function setOutFormat(string $outFormat): GetListOfErasedMessages
    {
        $this->outFormat = $outFormat;
        return $this;
    }
}
