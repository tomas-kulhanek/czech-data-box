<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\Request\IRequest;
use TomasKulhanek\CzechDataBox\Traits\Dummy;

/**
 * Class GetPasswordInfo
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:GetPasswordInfo",namespace="http://isds.czechpoint.cz/v20")
 */
class GetPasswordInfo implements IRequest
{
    use Dummy;
}
