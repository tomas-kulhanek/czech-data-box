<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Response;

use TomasKulhanek\CzechDataBox\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;
use TomasKulhanek\Serializer\Utils\SplFileInfo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class GetDataBoxList
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:GetDataBoxListResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class GetDataBoxList extends IResponse
{

    use DataBoxStatus;

    /**
     * @Serializer\SkipWhenEmpty()
     * @Serializer\Type("base64File")
     * @Serializer\SerializedName("p:dblData")
     * @Serializer\XmlElement(cdata=false)
     */
    protected ?SplFileInfo $data = null;

    public function getData(): ?SplFileInfo
    {
        return $this->data;
    }

    public function setData(SplFileInfo $data): GetDataBoxList
    {
        $this->data = $data;
        return $this;
    }

}
