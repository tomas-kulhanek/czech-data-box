<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\Request\IRequest;
use TomasKulhanek\CzechDataBox\Traits\Dummy;

/**
 * Class GetOwnerInfoFromLogin
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:GetOwnerInfoFromLogin",namespace="http://isds.czechpoint.cz/v20")
 */
class GetOwnerInfoFromLogin implements IRequest
{
    use Dummy;
}
