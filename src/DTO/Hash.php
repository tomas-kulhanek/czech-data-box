<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\Serializer\Utils\SplFileInfo;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'dmHash')]
class Hash
{
    #[Serializer\Type('base64File')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\XmlValue]
    protected SplFileInfo $value;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('algorithm')]
    #[Serializer\XmlAttribute]
    #[Assert\NotBlank(allowNull: false)]
    protected string $algorithm;

    public function getValue(): SplFileInfo
    {
        return $this->value;
    }

    public function setValue(SplFileInfo $value): Hash
    {
        $this->value = $value;
        return $this;
    }

    public function getAlgorithm(): string
    {
        return $this->algorithm;
    }

    public function setAlgorithm(string $algorithm): Hash
    {
        $this->algorithm = $algorithm;
        return $this;
    }
}
