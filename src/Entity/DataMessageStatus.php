<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Entity;

use TomasKulhanek\CzechDataBox\IResponseStatus;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class DataMessageStatus
 *
 * @Serializer\XmlRoot(name="p:dmStatus")
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 */
class DataMessageStatus implements IResponseStatus
{

    /**
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dmStatusCode")
     */
    protected string $code;

    /**
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dmStatusMessage")
     */
    protected string $message;

    /**
     * @Serializer\Type("string")
     * @Serializer\SkipWhenEmpty()
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dmStatusRefNumber")
     */
    protected ?string $refNumber = null;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): DataMessageStatus
    {
        $this->code = $code;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): DataMessageStatus
    {
        $this->message = $message;
        return $this;
    }

    public function getRefNumber(): ?string
    {
        return $this->refNumber;
    }

    public function setRefNumber(?string $refNumber): DataMessageStatus
    {
        $this->refNumber = $refNumber;
        return $this;
    }

    public function isOk(): bool
    {
        return substr($this->getCode(), 0, 2) === '00';
    }

}
