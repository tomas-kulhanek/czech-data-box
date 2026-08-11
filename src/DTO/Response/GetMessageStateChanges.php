<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\StateChangeRecord;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'GetMessageStateChangesResponse')]
class GetMessageStateChanges extends DataMessageResponse
{
    /**
     * @var StateChangeRecord[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\StateChangeRecord>')]
    #[Serializer\XmlList(entry: 'dmRecord', inline: false)]
    #[Serializer\SerializedName('dmRecords')]
    #[Assert\All([
        new Assert\Type(StateChangeRecord::class)
    ])]
    #[Assert\Valid()]
    protected array $record = [];

    /**
     * @return StateChangeRecord[]
     */
    public function getRecords(): array
    {
        return $this->record;
    }
}
