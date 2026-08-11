<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'NewAccessData2Response')]
class NewAccessData2 extends Response
{
    use DataBoxStatus;

    /**
     * ID uživatele nově vydaných přístupových údajů.
     */
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dbUserID')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $userId = null;

    /**
     * Identifikátor zásilky s přístupovými údaji, samotné údaje ISDS webovou službou nevrací.
     */
    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dbAccessDataId')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?string $accessDataId = null;

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function getAccessDataId(): ?string
    {
        return $this->accessDataId;
    }
}
