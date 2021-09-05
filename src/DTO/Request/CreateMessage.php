<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\Envelope;
use TomasKulhanek\CzechDataBox\DTO\File;
use TomasKulhanek\CzechDataBox\DTO\Recipient;
use TomasKulhanek\CzechDataBox\DTO\Request\IRequest;
use TomasKulhanek\CzechDataBox\Traits\GetMainFile;

/**
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:CreateMultipleMessage",namespace="http://isds.czechpoint.cz/v20")
 * @Serializer\AccessorOrder("custom",custom={"recipients","envelope","files"})
 */
class CreateMessage implements IRequest
{
    use GetMainFile;

    /**
     * @Serializer\Type("TomasKulhanek\CzechDataBox\DTO\Envelope")
     * @Serializer\SerializedName("p:dmEnvelope")
     * @Serializer\XmlElement(cdata=false)
     */
    protected Envelope $envelope;

    /**
     * @var File[]
     * @Serializer\Type("array<TomasKulhanek\CzechDataBox\DTO\File>")
     * @Serializer\XmlList(entry="dmFile", inline=false,namespace="http://isds.czechpoint.cz/v20")
     * @Serializer\SerializedName("dmFiles")
     * @Serializer\XmlElement(cdata=false,namespace="http://isds.czechpoint.cz/v20")
     */
    protected array $files = [];

    /**
     * @var Recipient[]
     * @Serializer\Type("array<TomasKulhanek\CzechDataBox\DTO\Recipient>")
     * @Serializer\XmlList(entry="dmRecipient", inline=false,namespace="http://isds.czechpoint.cz/v20")
     * @Serializer\SerializedName("dmRecipients")
     * @Serializer\XmlElement(cdata=false,namespace="http://isds.czechpoint.cz/v20")
     */
    protected array $recipients = [];

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
     * @return File[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param File[] $files
     * @return CreateMessage
     */
    public function setFiles(array $files): CreateMessage
    {
        $this->files = $files;
        return $this;
    }

    /**
     * @return Recipient[]
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * @param Recipient[] $recipients
     * @return CreateMessage
     */
    public function setRecipients(array $recipients): CreateMessage
    {
        $this->recipients = $recipients;
        return $this;
    }

    public function addRecipient(Recipient $recipient): CreateMessage
    {
        $this->recipients[] = $recipient;
        return $this;
    }

    public function addFile(File $file): CreateMessage
    {
        $this->files[] = $file;
        return $this;
    }
}
