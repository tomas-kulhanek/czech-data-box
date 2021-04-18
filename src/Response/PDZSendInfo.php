<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Response;

use TomasKulhanek\CzechDataBox\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class PDZInfo
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:PDZSendInfoResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class PDZSendInfo extends IResponse
{

    use DataBoxStatus;

    /**
     * @Serializer\Type("bool")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("p:PDZsiResult")
     */
    protected bool $result;

    public function isResult(): bool
    {
        return $this->result;
    }

}
