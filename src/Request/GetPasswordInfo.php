<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Request;

use TomasKulhanek\CzechDataBox\IRequest;
use TomasKulhanek\CzechDataBox\Traits\Dummy;
use JMS\Serializer\Annotation as Serializer;

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
