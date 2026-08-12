<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\MessageRecord;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'GetListOfSentMessagesResponse')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['records', 'status'])]
class GetListOfSentMessages extends DataMessageResponse
{
    /**
     * @var MessageRecord[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\MessageRecord>')]
    #[Serializer\XmlList(entry: 'dmRecord', inline: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmRecords')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Assert\All([
        new Assert\Type(type: MessageRecord::class)
    ])]
    #[Assert\Valid()]
    protected array $records = [];

    /**
     * @return MessageRecord[]
     */
    public function getRecord(): array
    {
        return $this->records;
    }
}
