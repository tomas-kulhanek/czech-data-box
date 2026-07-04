<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
<<<<<<<< HEAD:src/DTO/Request/UploadAttachment.php
use TomasKulhanek\CzechDataBox\DTO\BigAttachment;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'UploadAttachment')]
class UploadAttachment implements IRequest
{
    #[Serializer\Type(BigAttachment::class)]
    #[Serializer\SerializedName('dmFile')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\Valid()]
    protected BigAttachment $file;

    public function getFile(): BigAttachment
========
use TomasKulhanek\CzechDataBox\DTO\OwnerInfoExt2;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'GetOwnerInfoFromLogin2Response')]
class GetOwnerInfoFromLogin2 extends IResponse
{
    use DataBoxStatus;

    #[Serializer\Type(OwnerInfoExt2::class)]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbOwnerInfo')]
    #[Assert\Valid()]
    protected OwnerInfoExt2 $ownerInfo;

    public function getOwnerInfo(): OwnerInfoExt2
>>>>>>>> origin/main:src/DTO/Response/GetOwnerInfoFromLogin2.php
    {
        return $this->file;
    }
<<<<<<<< HEAD:src/DTO/Request/UploadAttachment.php

    public function setFile(BigAttachment $file): UploadAttachment
    {
        $this->file = $file;
        return $this;
    }
========
>>>>>>>> origin/main:src/DTO/Response/GetOwnerInfoFromLogin2.php
}
