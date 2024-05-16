<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\Response\IResponseStatus;

#[Serializer\XmlRoot(name: 'p:dmStatus')]
#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
class DataMessageStatus implements IResponseStatus
{
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmStatusCode')]
    #[Assert\NotBlank(allowNull: false)]
    protected string $code;

    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmStatusMessage')]
    #[Assert\NotBlank(allowNull: false)]
    protected string $message;

    #[Serializer\Type('string')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmStatusRefNumber')]
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
        return str_starts_with($this->getCode(), '00');
    }
}
