<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Entity;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class Recipient
 *
 * @Serializer\XmlRoot(name="p:dmRecipient")
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 */
class Recipient
{

    /**
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dbIDRecipient")
     */
    protected string $dataBoxId;

    /**
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dmRecipientOrgUnit")
     * @Serializer\SkipWhenEmpty
     */
    protected ?string $orgUnit = null;

    /**
     * @Serializer\Type("int")
     * @Serializer\SkipWhenEmpty
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dmRecipientOrgUnitNum")
     */
    protected ?int $orgUnitNum = null;

    /**
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dmToHands")
     */
    protected string $toHand;

    public function getDataBoxId(): string
    {
        return $this->dataBoxId;
    }

    public function setDataBoxId(string $dataBoxId): Recipient
    {
        $this->dataBoxId = $dataBoxId;
        return $this;
    }

    public function getOrgUnit(): ?string
    {
        return $this->orgUnit;
    }

    public function setOrgUnit(?string $orgUnit): Recipient
    {
        $this->orgUnit = $orgUnit;
        return $this;
    }

    public function getOrgUnitNum(): ?int
    {
        return $this->orgUnitNum;
    }

    public function setOrgUnitNum(?int $orgUnitNum): Recipient
    {
        $this->orgUnitNum = $orgUnitNum;
        return $this;
    }

    public function getToHand(): string
    {
        return $this->toHand;
    }

    public function setToHand(string $toHand): Recipient
    {
        $this->toHand = $toHand;
        return $this;
    }

}
