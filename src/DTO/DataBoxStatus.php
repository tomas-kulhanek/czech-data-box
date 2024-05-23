<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\Response\IResponseStatus;

#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'dbStatus')]
#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
class DataBoxStatus implements IResponseStatus
{
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbStatusCode')]
    #[Assert\NotBlank(allowNull: false)]
    protected string $code;

    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbStatusMessage')]
    #[Assert\NotBlank(allowNull: false)]
    protected string $message;

    #[Serializer\Type('string')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbStatusRefNumber')]
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
        return str_starts_with($this->getCode(), '00');
    }
}
