<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\Envelope;
use TomasKulhanek\CzechDataBox\DTO\File;
use TomasKulhanek\CzechDataBox\DTO\Recipient;
use TomasKulhanek\CzechDataBox\Traits\GetMainFile;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'CreateMultipleMessage')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['recipients', 'envelope', 'files'])]
class CreateMessage implements IRequest
{
    use GetMainFile;

    #[Serializer\Type(Envelope::class)]
    #[Serializer\SerializedName('dmEnvelope')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\Valid()]
    protected Envelope $envelope;

    /**
     * @var File[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\File>')]
    #[Serializer\XmlList(entry: 'dmFile', inline: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmFiles')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\All([
        new Assert\Type(File::class)
    ])]
    #[Assert\Valid()]
    protected array $files = [];

    /**
     * @var Recipient[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\Recipient>')]
    #[Serializer\XmlList(entry: 'dmRecipient', inline: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmRecipients')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\All([
        new Assert\Type(Recipient::class)
    ])]
    #[Assert\Valid()]
    protected array $recipients = [];

    public function getEnvelope(): Envelope
    {
        return $this->envelope;
    }

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
