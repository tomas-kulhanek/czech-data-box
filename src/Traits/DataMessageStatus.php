<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Traits;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\Response\ResponseStatus;

trait DataMessageStatus
{
    #[Serializer\Type(\TomasKulhanek\CzechDataBox\DTO\DataMessageStatus::class)]
    #[Serializer\SerializedName('dmStatus')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\Valid()]
    protected ResponseStatus $status;

    public function getStatus(): ResponseStatus
    {
        return $this->status;
    }

    public function setStatus(ResponseStatus $status): self
    {
        $this->status = $status;
        return $this;
    }
}
