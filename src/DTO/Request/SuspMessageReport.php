<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'SuspMessageReport')]
#[Serializer\AccessorOrder(
    order: 'custom',
    custom: ['dataMessageId', 'reporterName', 'reporterMail', 'reporterPhone', 'allowComplete', 'note']
)]
class SuspMessageReport extends DataMessageRequest
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('repName')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $reporterName = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('repMail')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $reporterMail = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('repTel')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $reporterPhone = null;

    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('allowComplete')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected bool $allowComplete;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('note')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $note = null;

    public function getReporterName(): ?string
    {
        return $this->reporterName;
    }

    public function setReporterName(?string $reporterName): SuspMessageReport
    {
        $this->reporterName = $reporterName;
        return $this;
    }

    public function getReporterMail(): ?string
    {
        return $this->reporterMail;
    }

    public function setReporterMail(?string $reporterMail): SuspMessageReport
    {
        $this->reporterMail = $reporterMail;
        return $this;
    }

    public function getReporterPhone(): ?string
    {
        return $this->reporterPhone;
    }

    public function setReporterPhone(?string $reporterPhone): SuspMessageReport
    {
        $this->reporterPhone = $reporterPhone;
        return $this;
    }

    public function isAllowComplete(): bool
    {
        return $this->allowComplete;
    }

    public function setAllowComplete(bool $allowComplete): SuspMessageReport
    {
        $this->allowComplete = $allowComplete;
        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): SuspMessageReport
    {
        $this->note = $note;
        return $this;
    }
}
