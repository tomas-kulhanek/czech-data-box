<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Response;

use TomasKulhanek\CzechDataBox\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;
use TomasKulhanek\CzechDataBox\Traits\Signature;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class SignedSentMessageDownload
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:SignedSentMessageDownloadResponse", namespace="http://isds.czechpoint.cz/v20")
 * @Serializer\AccessorOrder("custom",custom={"signature","status"})
 */
class SignedSentMessageDownload extends IResponse
{

    use DataMessageStatus;
    use Signature;

    public function setStatus(\TomasKulhanek\CzechDataBox\Entity\DataMessageStatus $status): SignedSentMessageDownload
    {
        $this->status = $status;
        return $this;
    }

}
