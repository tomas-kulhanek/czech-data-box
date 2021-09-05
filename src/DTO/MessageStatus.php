<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\Response\IResponseStatus;

/**
 * Class MessageStatus
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:dmSingleStatus")
 */
class MessageStatus
{
    /**
     * @Serializer\Type("TomasKulhanek\CzechDataBox\DTO\DataMessageStatus")
     * @Serializer\SerializedName("dmStatus")
     * @Serializer\XmlElement(cdata=false,namespace="http://isds.czechpoint.cz/v20")
     */
    protected IResponseStatus $status;

    /**
     * @Serializer\SkipWhenEmpty
     * @Serializer\Type("string")
     * @Serializer\SerializedName("p:dmID")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?string $dataMessageId = null;

    public function getStatus(): IResponseStatus
    {
        return $this->status;
    }

    public function setStatus(DataMessageStatus $status): MessageStatus
    {
        $this->status = $status;
        return $this;
    }

    public function getDataMessageId(): ?string
    {
        return $this->dataMessageId;
    }

    public function setDataMessageId(?string $dataMessageId): MessageStatus
    {
        $this->dataMessageId = $dataMessageId;
        return $this;
    }
}
