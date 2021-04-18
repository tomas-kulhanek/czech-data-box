<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Request;

use TomasKulhanek\CzechDataBox\IRequest;
use TomasKulhanek\Serializer\Utils\SplFileInfo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class ResignISDSDocument
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:Re-signISDSDocument",namespace="http://isds.czechpoint.cz/v20")
 */
class ResignISDSDocument implements IRequest
{

    /**
     * @Serializer\Type("base64File")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:dmDoc")
     */
    protected SplFileInfo $document;

    public function getDocument(): SplFileInfo
    {
        return $this->document;
    }

    public function setDocument(SplFileInfo $document): ResignISDSDocument
    {
        $this->document = $document;
        return $this;
    }

}
