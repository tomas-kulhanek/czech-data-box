<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\MessageStatus;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'CreateMultipleMessageResponse')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['messageStatus', 'status'])]
class CreateMessage extends IResponse
{
    use DataMessageStatus;

    /**
     * @var MessageStatus[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\MessageStatus>')]
    #[Serializer\XmlList(entry: 'dmSingleStatus', inline: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmMultipleStatus')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\All([
        new Assert\Type(type: MessageStatus::class)
    ])]
    #[Assert\Valid()]
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
