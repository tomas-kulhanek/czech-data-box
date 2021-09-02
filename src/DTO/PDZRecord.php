<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class CreditRecord
 *
 * @Serializer\XmlRoot(name="p:dbPDZRecord")
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 */
class PDZRecord
{
    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("p:PDZType")
     * @Serializer\XmlElement(cdata=false)
     */
    protected string $type;

    /**
     * @Serializer\SkipWhenEmpty
     * @Serializer\Type("string")
     * @Serializer\SerializedName("p:PDZRecip")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?string $recipient = null;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("p:PDZPayer")
     * @Serializer\XmlElement(cdata=false)
     */
    protected string $payer;

    /**
     * @Serializer\SkipWhenEmpty
     * @Serializer\Type("DateTimeImmutable<'Y-m-d\TH:i:s.uP','Europe/Prague'>")
     * @Serializer\SerializedName("p:PDZExpire")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?DateTimeImmutable $expire = null;

    /**
     * @Serializer\SkipWhenEmpty
     * @Serializer\Type("int")
     * @Serializer\SerializedName("p:PDZCnt")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?int $count = null;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("p:ODZIdent")
     * @Serializer\XmlElement(cdata=false)
     */
    protected string $ident;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): PDZRecord
    {
        $this->type = $type;
        return $this;
    }

    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    public function setRecipient(?string $recipient): PDZRecord
    {
        $this->recipient = $recipient;
        return $this;
    }

    public function getPayer(): string
    {
        return $this->payer;
    }

    public function setPayer(string $payer): PDZRecord
    {
        $this->payer = $payer;
        return $this;
    }

    public function getExpire(): ?DateTimeImmutable
    {
        return $this->expire;
    }

    public function setExpire(?DateTimeImmutable $expire): PDZRecord
    {
        $this->expire = $expire;
        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(?int $count): PDZRecord
    {
        $this->count = $count;
        return $this;
    }

    public function getIdent(): string
    {
        return $this->ident;
    }

    public function setIdent(string $ident): PDZRecord
    {
        $this->ident = $ident;
        return $this;
    }
}
