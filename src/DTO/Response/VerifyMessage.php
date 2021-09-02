<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\Hash;
use TomasKulhanek\CzechDataBox\DTO\Response\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

/**
 * Class VerifyMessage
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:VerifyMessageResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class VerifyMessage extends IResponse
{
    use DataMessageStatus;

    /**
     * @Serializer\Type("TomasKulhanek\CzechDataBox\DTO\Hash")
     * @Serializer\SerializedName("p:dmHash")
     * @Serializer\XmlElement(cdata=false)
     */
    protected Hash $hash;

    public function setStatus(\TomasKulhanek\CzechDataBox\DTO\DataMessageStatus $status): VerifyMessage
    {
        $this->status = $status;
        return $this;
    }

    public function getHash(): Hash
    {
        return $this->hash;
    }

    public function setHash(Hash $hash): VerifyMessage
    {
        $this->hash = $hash;
        return $this;
    }
}
