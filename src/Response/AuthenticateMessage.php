<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Response;

use TomasKulhanek\CzechDataBox\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class ChangeISDSPassword
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:AuthenticateMessageResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class AuthenticateMessage extends IResponse
{

    use DataMessageStatus;

    /**
     * @Serializer\Type("bool")
     * @Serializer\SerializedName("p:dmAuthResult")
     */
    protected bool $authenticated;

    public function isAuthenticated(): bool
    {
        return $this->authenticated;
    }

    public function setAuthenticated(bool $authenticated): AuthenticateMessage
    {
        $this->authenticated = $authenticated;
        return $this;
    }

}
