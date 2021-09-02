<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\Traits\DataMessageEnvelope;
use TomasKulhanek\CzechDataBox\Traits\GetMainFile;

class ReceivedMessageEnvelope
{
    use GetMainFile;
    use DataMessageEnvelope;

    /**
     * @var File[]
     * @Serializer\Type("array<TomasKulhanek\CzechDataBox\DTO\File>")
     * @Serializer\XmlList(entry="dmFile", inline=false,namespace="http://isds.czechpoint.cz/v20")
     * @Serializer\SerializedName("dmFiles")
     * @Serializer\XmlElement(cdata=false,namespace="http://isds.czechpoint.cz/v20")
     */
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
     * @return ReceivedMessageEnvelope
     */
    public function setFiles(array $files): ReceivedMessageEnvelope
    {
        $this->files = $files;
        return $this;
    }
}
