<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'p:GetPasswordInfoResponse', namespace: 'http://isds.czechpoint.cz/v20')]
class GetPasswordInfo extends IResponse
{
    use DataBoxStatus;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d\\TH:i:s.uP','Europe/Prague'>")]
    #[Serializer\XmlElement(cdata: false)]
    #[Serializer\SerializedName('p:pswExpDate')]
    protected ?DateTimeImmutable $passwordExpiry = null;

    public function getPasswordExpiry(): ?DateTimeImmutable
    {
        return $this->passwordExpiry;
    }
}
