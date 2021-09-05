<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\MessageStatus;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

/**
 * Class CreateMessage
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:CreateMultipleMessageResponse", namespace="http://isds.czechpoint.cz/v20")
 * @Serializer\AccessorOrder("custom",custom={"messageStatus","status"})
 */
class CreateMessage extends IResponse
{
    use DataMessageStatus;

    /**
     * @var MessageStatus[]
     * @Serializer\Type("array<TomasKulhanek\CzechDataBox\DTO\MessageStatus>")
     * @Serializer\XmlList(entry="dmSingleStatus", inline=false, namespace="http://isds.czechpoint.cz/v20")
     * @Serializer\SerializedName("dmMultipleStatus")
     * @Serializer\XmlElement(cdata=false,namespace="http://isds.czechpoint.cz/v20")
     */
    protected array $multipleStatus = [];

    /**
     * @return MessageStatus[]
     */
    public function getMultipleStatus(): array
    {
        return $this->multipleStatus;
    }

    public function setStatus(\TomasKulhanek\CzechDataBox\DTO\DataMessageStatus $status): CreateMessage
    {
        $this->status = $status;
        return $this;
    }

    public function isOk(): bool
    {
        if (!$this->getStatus()->isOk()) {
            return false;
        }
        /** @var MessageStatus $messageStatus */
        foreach ($this->getMultipleStatus() as $messageStatus) {
            if (!$messageStatus->getStatus()->isOk()) {
                return false;
            }
        }
        return true;
    }
}
