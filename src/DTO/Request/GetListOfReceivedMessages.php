<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Enum\FilterEnum;
use TomasKulhanek\CzechDataBox\Traits\StatusFilter;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'GetListOfReceivedMessages', namespace: 'https://isds.czechpoint.cz/v20')]
class GetListOfReceivedMessages implements IRequest
{
    use StatusFilter;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d\\TH:i:s.uP','Europe/Prague'>")]
    #[Serializer\SerializedName('dmFromTime')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?DateTimeImmutable $listFrom = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d\\TH:i:s.uP','Europe/Prague'>")]
    #[Serializer\SerializedName('dmToTime')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    protected ?DateTimeImmutable $listTo = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmRecipientOrgUnitNum')]
    protected ?int $recipientOrgUnitNum = null;

    #[Serializer\Type('integer')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmStatusFilter')]
    protected int $statusFilter = -1;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmOffset')]
    protected ?int $offset = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmLimit')]
    protected ?int $limit = null;

    public function getListFrom(): ?DateTimeImmutable
    {
        return $this->listFrom;
    }

    public function setListFrom(?DateTimeImmutable $listFrom): GetListOfReceivedMessages
    {
        $this->listFrom = $listFrom;
        return $this;
    }

    public function getListTo(): ?DateTimeImmutable
    {
        return $this->listTo;
    }

    public function setListTo(?DateTimeImmutable $listTo): GetListOfReceivedMessages
    {
        $this->listTo = $listTo;
        return $this;
    }

    public function getRecipientOrgUnitNum(): ?int
    {
        return $this->recipientOrgUnitNum;
    }

    public function setRecipientOrgUnitNum(?int $recipientOrgUnitNum): GetListOfReceivedMessages
    {
        $this->recipientOrgUnitNum = $recipientOrgUnitNum;
        return $this;
    }

    /**
     * @return non-empty-list<FilterEnum>
     */
    public function getStatusFilter(): array
    {
        return $this->decodeFilters($this->statusFilter);
    }

    public function setStatusFilter(FilterEnum ...$statuses): GetListOfReceivedMessages
    {
        $this->statusFilter = $this->encodeFilters(...$statuses);
        return $this;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function setOffset(?int $offset): GetListOfReceivedMessages
    {
        $this->offset = $offset;
        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): GetListOfReceivedMessages
    {
        $this->limit = $limit;
        return $this;
    }
}
