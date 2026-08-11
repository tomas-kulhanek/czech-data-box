<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\BigAttachment;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'UploadAttachment')]
class UploadAttachment implements Request
{
    #[Serializer\Type(BigAttachment::class)]
    #[Serializer\SerializedName('dmFile')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\Valid()]
    protected BigAttachment $file;

    public function getFile(): BigAttachment
    {
        return $this->file;
    }

    public function setFile(BigAttachment $file): UploadAttachment
    {
        $this->file = $file;
        return $this;
    }
}
