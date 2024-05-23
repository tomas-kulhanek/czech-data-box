<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\PDZRecord;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'PDZInfoResponse')]
class PDZInfo extends IResponse
{
    use DataBoxStatus;

    /**
     * @var PDZRecord[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\PDZRecord>')]
    #[Serializer\XmlList(entry: 'dbPDZRecord', inline: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbPDZRecords')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\All([
        new Assert\Type(type: PDZRecord::class)
    ])]
    #[Assert\Valid()]
    protected array $pdzRecord = [];

    /**
     * @return PDZRecord[]
     */
    public function getPdzRecord(): array
    {
        return $this->pdzRecord;
    }

    /**
     * @param PDZRecord[] $pdzRecord
     * @return PDZInfo
     */
    public function setPdzRecord(array $pdzRecord): PDZInfo
    {
        $this->pdzRecord = $pdzRecord;
        return $this;
    }
}
