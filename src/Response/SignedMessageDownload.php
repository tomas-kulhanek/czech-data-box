<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Response;

use TomasKulhanek\CzechDataBox\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;
use TomasKulhanek\CzechDataBox\Traits\Signature;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class SignedMessageDownload
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:SignedMessageDownloadResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class SignedMessageDownload extends IResponse
{

    use DataMessageStatus;
    use Signature;

    public function setStatus(\TomasKulhanek\CzechDataBox\Entity\DataMessageStatus $status): SignedMessageDownload
    {
        $this->status = $status;
        return $this;
    }

}
