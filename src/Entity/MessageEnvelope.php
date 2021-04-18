<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use TomasKulhanek\CzechDataBox\Traits\DataMessageEnvelope;
use TomasKulhanek\CzechDataBox\Traits\GetMainFile;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class MessageEnvelope
 *
 * @Serializer\XmlRoot(name="p:dmDm")
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 */
class MessageEnvelope
{

    use GetMainFile;
    use DataMessageEnvelope;

    /**
     * @var Collection<int, File>
     * @Serializer\Type("ArrayCollection<TomasKulhanek\CzechDataBox\Entity\File>")
     * @Serializer\XmlList(entry="dmFile", inline=false,namespace="http://isds.czechpoint.cz/v20")
     * @Serializer\SerializedName("dmFiles")
     * @Serializer\XmlElement(cdata=false,namespace="http://isds.czechpoint.cz/v20")
     */
    protected Collection $files;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    /**
     * @return Collection<int, File>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    /**
     * @param Collection<int, File> $files
     * @return MessageEnvelope
     */
    public function setFiles(Collection $files): MessageEnvelope
    {
        $this->files = $files;
        return $this;
    }

}
