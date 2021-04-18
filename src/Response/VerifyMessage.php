<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Response;

use TomasKulhanek\CzechDataBox\Entity\Hash;
use TomasKulhanek\CzechDataBox\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;
use JMS\Serializer\Annotation as Serializer;

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
     * @Serializer\Type("TomasKulhanek\CzechDataBox\Entity\Hash")
     * @Serializer\SerializedName("p:dmHash")
     * @Serializer\XmlElement(cdata=false)
     */
    protected Hash $hash;

    public function setStatus(\TomasKulhanek\CzechDataBox\Entity\DataMessageStatus $status): VerifyMessage
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
