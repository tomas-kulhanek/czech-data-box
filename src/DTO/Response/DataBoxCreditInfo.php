<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\CreditRecord;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'DataBoxCreditInfoResponse')]
class DataBoxCreditInfo extends IResponse
{
    use DataBoxStatus;

    #[Serializer\Type('int')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('currentCredit')]
    protected ?int $currentCredit = null;

    #[Serializer\Type('string')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('notifEmail')]
    protected ?string $notifyEmail = null;

    /**
     * @var CreditRecord[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\CreditRecord>')]
    #[Serializer\XmlList(entry: 'ciRecord', inline: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('ciRecords')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\All([
        new Assert\Type(type: CreditRecord::class)
    ])]
    #[Assert\Valid()]
    protected array $records = [];

    public function getCurrentCredit(): ?int
    {
        return $this->currentCredit;
    }

    public function getNotifyEmail(): ?string
    {
        return $this->notifyEmail;
    }

    /**
     * @return CreditRecord[]
     */
    public function getRecords(): array
    {
        return $this->records;
    }
}
