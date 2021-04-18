<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Request;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use TomasKulhanek\CzechDataBox\Entity\Envelope;
use TomasKulhanek\CzechDataBox\Entity\File;
use TomasKulhanek\CzechDataBox\Entity\Recipient;
use TomasKulhanek\CzechDataBox\IRequest;
use TomasKulhanek\CzechDataBox\Traits\GetMainFile;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class CreateMessage
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:CreateMultipleMessage",namespace="http://isds.czechpoint.cz/v20")
 * @Serializer\AccessorOrder("custom",custom={"recipients","envelope","files"})
 */
class CreateMessage implements IRequest
{

    use GetMainFile;

    /**
     * @Serializer\Type("TomasKulhanek\CzechDataBox\Entity\Envelope")
     * @Serializer\SerializedName("p:dmEnvelope")
     * @Serializer\XmlElement(cdata=false)
     */
    protected Envelope $envelope;

    /**
     * @var Collection<int,File>
     * @Serializer\Type("ArrayCollection<TomasKulhanek\CzechDataBox\Entity\File>")
     * @Serializer\XmlList(entry="dmFile", inline=false,namespace="http://isds.czechpoint.cz/v20")
     * @Serializer\SerializedName("dmFiles")
     * @Serializer\XmlElement(cdata=false,namespace="http://isds.czechpoint.cz/v20")
     */
    protected Collection $files;

    /**
     * @var Collection<int,Recipient>
     * @Serializer\Type("ArrayCollection<TomasKulhanek\CzechDataBox\Entity\Recipient>")
     * @Serializer\XmlList(entry="dmRecipient", inline=false,namespace="http://isds.czechpoint.cz/v20")
     * @Serializer\SerializedName("dmRecipients")
     * @Serializer\XmlElement(cdata=false,namespace="http://isds.czechpoint.cz/v20")
     */
    protected Collection $recipients;

    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->recipients = new ArrayCollection();
    }

    /**
     * @return Envelope
     */
    public function getEnvelope(): Envelope
    {
        return $this->envelope;
    }

    /**
     * @param Envelope $envelope
     * @return CreateMessage
     */
    public function setEnvelope(Envelope $envelope): CreateMessage
    {
        $this->envelope = $envelope;
        return $this;
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
     * @return CreateMessage
     */
    public function setFiles(Collection $files): CreateMessage
    {
        $this->files = $files;
        return $this;
    }

    /**
     * @return Collection<int, Recipient>
     */
    public function getRecipients(): Collection
    {
        return $this->recipients;
    }

    /**
     * @param Collection<int, Recipient> $recipients
     * @return CreateMessage
     */
    public function setRecipients(Collection $recipients): CreateMessage
    {
        $this->recipients = $recipients;
        return $this;
    }

    public function addRecipient(Recipient $recipient): CreateMessage
    {
        if (!$this->recipients->contains($recipient)) {
            $this->recipients->add($recipient);
        }
        return $this;
    }

    public function addFile(File $file): CreateMessage
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
        }
        return $this;
    }

}
