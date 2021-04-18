<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Response;

use DateTimeImmutable;
use TomasKulhanek\CzechDataBox\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;
use TomasKulhanek\Serializer\Utils\SplFileInfo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class ResignISDSDocument
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:Re-signISDSDocumentResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class ResignISDSDocument extends IResponse
{

    use DataMessageStatus;

    /**
     * @Serializer\Type("base64File")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dmResultDoc")
     */
    protected SplFileInfo $document;

    /**
     * @Serializer\SkipWhenEmpty()
     * @Serializer\Type("DateTimeImmutable<'Y-m-d'>")
     * @Serializer\SerializedName("p:dmValidTo")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?DateTimeImmutable $validTo = null;

    public function setStatus(\TomasKulhanek\CzechDataBox\Entity\DataMessageStatus $status): ResignISDSDocument
    {
        $this->status = $status;
        return $this;
    }

    public function getDocument(): SplFileInfo
    {
        return $this->document;
    }

    public function setDocument(SplFileInfo $document): ResignISDSDocument
    {
        $this->document = $document;
        return $this;
    }

    public function getValidTo(): ?DateTimeImmutable
    {
        return $this->validTo;
    }

    public function setValidTo(?DateTimeImmutable $validTo): ResignISDSDocument
    {
        $this->validTo = $validTo;
        return $this;
    }

}
