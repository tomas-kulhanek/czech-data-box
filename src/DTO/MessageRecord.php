<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'dmRecord')]
#[Serializer\AccessorOrder(order: 'custom', custom: [
    'ordinal',
    'dataMessageId',
    'senderId',
    'sender',
    'senderAddress',
    'senderType',
    'recipient',
    'recipientAddress',
    'ambiguousRecipient',
    'senderOrgUnit',
    'senderOrgUnitNum',
    'recipientId',
    'recipientOrgUnit',
    'recipientOrgUnitNum',
    'toHands',
    'annotation',
    'recipientRefNumber',
    'senderRefNumber',
    'recipientIdent',
    'senderIdent',
    'legalTitleLaw',
    'legalTitleYear',
    'legalTitleSect',
    'legalTitlePar',
    'legalTitlePoint',
    'personalDelivery',
    'allowSubstDelivery',
    'messageStatus',
    'attachmentSize',
    'deliveryTime',
    'acceptanceTime',
])]
class MessageRecord extends AbstractMessageEnvelope
{
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\SerializedName('dmOrdinal')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?int $ordinal = null;

    #[Serializer\Type('int')]
    #[Serializer\SerializedName('dmMessageStatus')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Assert\PositiveOrZero()]
    protected ?int $messageStatus = null;

    #[Serializer\Type('int')]
    #[Serializer\SerializedName('dmAttachmentSize')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Assert\Positive()]
    protected ?int $attachmentSize = null;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d\\TH:i:s.uP','Europe/Prague'>")]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmDeliveryTime')]
    #[Serializer\SkipWhenEmpty]
    protected ?DateTimeImmutable $deliveryTime = null;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d\\TH:i:s.uP','Europe/Prague'>")]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmAcceptanceTime')]
    #[Serializer\SkipWhenEmpty]
    protected ?DateTimeImmutable $acceptanceTime = null;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dmType')]
    #[Serializer\XmlAttribute]
    #[Assert\NotBlank(allowNull: false)]
    protected ?string $type = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('dmVODZ')]
    #[Serializer\XmlAttribute]
    protected ?bool $vodz = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\SerializedName('specMessFlag')]
    #[Serializer\XmlAttribute]
    protected ?int $specMessFlag = null;

    public function getOrdinal(): ?int
    {
        return $this->ordinal;
    }

    public function setOrdinal(?int $ordinal): MessageRecord
    {
        $this->ordinal = $ordinal;
        return $this;
    }

    public function getMessageStatus(): ?int
    {
        return $this->messageStatus;
    }

    public function setMessageStatus(?int $messageStatus): MessageRecord
    {
        $this->messageStatus = $messageStatus;
        return $this;
    }

    public function getAttachmentSize(): ?int
    {
        return $this->attachmentSize;
    }

    public function setAttachmentSize(?int $attachmentSize): MessageRecord
    {
        $this->attachmentSize = $attachmentSize;
        return $this;
    }

    public function getDeliveryTime(): ?DateTimeImmutable
    {
        return $this->deliveryTime;
    }

    public function setDeliveryTime(?DateTimeImmutable $deliveryTime): MessageRecord
    {
        $this->deliveryTime = $deliveryTime;
        return $this;
    }

    public function getAcceptanceTime(): ?DateTimeImmutable
    {
        return $this->acceptanceTime;
    }

    public function setAcceptanceTime(?DateTimeImmutable $acceptanceTime): MessageRecord
    {
        $this->acceptanceTime = $acceptanceTime;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): MessageRecord
    {
        $this->type = $type;
        return $this;
    }

    public function isVodz(): bool
    {
        return $this->vodz === true;
    }

    public function setVodz(?bool $vodz): MessageRecord
    {
        $this->vodz = $vodz;
        return $this;
    }

    public function getSpecMessFlag(): ?int
    {
        return $this->specMessFlag;
    }

    public function setSpecMessFlag(?int $specMessFlag): MessageRecord
    {
        $this->specMessFlag = $specMessFlag;
        return $this;
    }

    public function isSuspicious(): bool
    {
        return $this->specMessFlag === 1;
    }
}
