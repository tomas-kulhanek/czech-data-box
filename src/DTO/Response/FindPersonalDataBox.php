<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\PersonalOwnerInfo;
use TomasKulhanek\CzechDataBox\DTO\Response\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

/**
 * Class FindPersonalDataBoxResponse
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:FindPersonalDataBoxResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class FindPersonalDataBox extends IResponse
{
    use DataBoxStatus;

    /**
     * @var PersonalOwnerInfo[]
     * @Serializer\Type("array<TomasKulhanek\CzechDataBox\DTO\PersonalOwnerInfo>")
     * @Serializer\SkipWhenEmpty()
     * @Serializer\XmlList(entry="dbOwnerInfo", inline=false, namespace="http://isds.czechpoint.cz/v20")
     * @Serializer\SerializedName("dbResults")
     * @Serializer\XmlElement(cdata=false,namespace="http://isds.czechpoint.cz/v20")
     */
    protected $record;

    /**
     * @return PersonalOwnerInfo[]
     */
    public function getRecord(): array
    {
        return $this->record;
    }
}
