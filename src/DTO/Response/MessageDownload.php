<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\Response\IResponse;
use TomasKulhanek\CzechDataBox\DTO\ReturnedMessage;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

/**
 * Class MessageDownload
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:MessageDownloadResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class MessageDownload extends IResponse
{
    use DataMessageStatus;

    /**
     * @Serializer\SkipWhenEmpty
     * @Serializer\Type("TomasKulhanek\CzechDataBox\DTO\ReturnedMessage")
     * @Serializer\SerializedName("p:dmReturnedMessage")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?ReturnedMessage $returnedMessage;

    public function getReturnedMessage(): ?ReturnedMessage
    {
        return $this->returnedMessage;
    }

    public function setReturnedMessage(?ReturnedMessage $returnedMessage): MessageDownload
    {
        $this->returnedMessage = $returnedMessage;
        return $this;
    }
}
