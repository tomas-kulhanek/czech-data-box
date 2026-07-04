<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;

/**
 * Element dmPublishOwnID — souhlas odesílatele se zveřejněním vlastních
 * identifikačních údajů ve zprávě. Volitelný atribut IdLevel je bitová maska
 * určující rozsah zveřejněných údajů (userType, jméno, datum a místo
 * narození, adresa, robIdent) dle dmBaseTypes.xsd.
 */
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'dmPublishOwnID')]
#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
class PublishOwnId
{
    #[Serializer\Type('bool')]
    #[Serializer\XmlValue(cdata: false)]
    protected bool $value = false;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\SerializedName('IdLevel')]
    #[Serializer\XmlAttribute]
    protected ?int $idLevel = null;

    public function getValue(): bool
    {
        return $this->value;
    }

    public function setValue(bool $value): PublishOwnId
    {
        $this->value = $value;
        return $this;
    }

    public function getIdLevel(): ?int
    {
        return $this->idLevel;
    }

    public function setIdLevel(?int $idLevel): PublishOwnId
    {
        $this->idLevel = $idLevel;
        return $this;
    }
}
