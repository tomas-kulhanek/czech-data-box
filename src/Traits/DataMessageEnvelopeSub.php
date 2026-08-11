<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Traits;

use JMS\Serializer\Annotation as Serializer;

/**
 * Envelope of a single message, matching the gMessageEnvelopeSub group of dmBaseTypes.xsd.
 * On top of the shared elements it addresses one recipient, so it also carries the recipient
 * organisational unit. Envelopes of CreateMultipleMessage must not use this trait.
 */
trait DataMessageEnvelopeSub
{
    use MultipleMessageEnvelopeSub;

    #[Serializer\Type('string')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('dmRecipientOrgUnit')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $recipientOrgUnit = null;

    #[Serializer\Type('int')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\SerializedName('dmRecipientOrgUnitNum')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?int $recipientOrgUnitNum = null;

    public function getRecipientOrgUnit(): ?string
    {
        return $this->recipientOrgUnit;
    }

    public function setRecipientOrgUnit(?string $recipientOrgUnit): self
    {
        $this->recipientOrgUnit = $recipientOrgUnit;
        return $this;
    }

    public function getRecipientOrgUnitNum(): ?int
    {
        return $this->recipientOrgUnitNum;
    }

    public function setRecipientOrgUnitNum(?int $recipientOrgUnitNum): self
    {
        $this->recipientOrgUnitNum = $recipientOrgUnitNum;
        return $this;
    }
}
