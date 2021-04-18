<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Entity;

use TomasKulhanek\CzechDataBox\IResponseStatus;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class DataBoxStatus
 *
 * @Serializer\XmlRoot(name="p:dbStatus")
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 */
class DataBoxStatus implements IResponseStatus
{

    /**
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dbStatusCode")
     */
    protected string $code;

    /**
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dbStatusMessage")
     */
    protected string $message;

    /**
     * @Serializer\Type("string")
     * @Serializer\SkipWhenEmpty()
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dbStatusRefNumber")
     */
    protected ?string $refNumber = null;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): DataBoxStatus
    {
        $this->code = $code;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): DataBoxStatus
    {
        $this->message = $message;
        return $this;
    }

    public function getRefNumber(): ?string
    {
        return $this->refNumber;
    }

    public function setRefNumber(?string $refNumber): DataBoxStatus
    {
        $this->refNumber = $refNumber;
        return $this;
    }

    public function isOk(): bool
    {
        return substr($this->getCode(), 0, 2) === '00';
    }

}
