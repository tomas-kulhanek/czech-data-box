<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\Traits\DataMessageEnvelope;
use TomasKulhanek\CzechDataBox\Traits\GetMainFile;

#[Serializer\XmlRoot(name: 'p:dmDm')]
#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
class MessageEnvelope
{
    use GetMainFile;
    use DataMessageEnvelope;

    /**
     * @var File[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\File>')]
    #[Serializer\XmlList(entry: 'dmFile', inline: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmFiles')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Assert\All([
        new Assert\Type(type: \Hoa\File\File::class)
    ])]
    #[Assert\Valid()]
    protected array $files = [];

    /**
     * @return File[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param File[] $files
     * @return MessageEnvelope
     */
    public function setFiles(array $files): MessageEnvelope
    {
        $this->files = $files;
        return $this;
    }
}
