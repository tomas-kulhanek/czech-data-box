<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\NotificationRecord;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'GetListForNotificationsResponse')]
class GetListForNotifications extends DataMessageResponse
{
    /**
     * @var NotificationRecord[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\NotificationRecord>')]
    #[Serializer\XmlList(entry: 'ntfRecord', inline: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('ntfRecords')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Assert\All([
        new Assert\Type(type: NotificationRecord::class)
    ])]
    #[Assert\Valid()]
    protected array $records = [];

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('ntfListContinues')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?bool $listContinues = null;

    /**
     * @return NotificationRecord[]
     */
    public function getRecords(): array
    {
        return $this->records;
    }

    public function getListContinues(): ?bool
    {
        return $this->listContinues;
    }
}
