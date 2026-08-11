<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\ConstantRecord;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'GetConstantsResponse')]
class GetConstants extends Response
{
    use DataBoxStatus;

    /**
     * @var ConstantRecord[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\ConstantRecord>')]
    #[Serializer\XmlList(entry: 'constRecord', inline: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('constRecords')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\All([
        new Assert\Type(type: ConstantRecord::class)
    ])]
    #[Assert\Valid()]
    protected array $records = [];

    /**
     * @return ConstantRecord[]
     */
    public function getRecords(): array
    {
        return $this->records;
    }
}
