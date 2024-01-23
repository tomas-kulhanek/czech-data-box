<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\PersonalOwnerInfo;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'p:FindPersonalDataBoxResponse', namespace: 'http://isds.czechpoint.cz/v20')]
class FindPersonalDataBox extends IResponse
{
    use DataBoxStatus;

    /**
     * @var PersonalOwnerInfo[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\PersonalOwnerInfo>')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\XmlList(entry: 'dbOwnerInfo', inline: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbResults')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Assert\All([
        new Assert\Type(type: PersonalOwnerInfo::class)
    ])]
    #[Assert\Valid()]
    protected array $record = [];

    /**
     * @return PersonalOwnerInfo[]
     */
    public function getRecord(): array
    {
        return $this->record;
    }
}
