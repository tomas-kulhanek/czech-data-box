<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\MessageRecord;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'GetListOfSentMessagesResponse')]
class GetListOfSentMessages extends IResponse
{
    use DataMessageStatus;

    /**
     * @var MessageRecord[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\MessageRecord>')]
    #[Serializer\XmlList(entry: 'dmRecord', inline: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmRecords')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
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
