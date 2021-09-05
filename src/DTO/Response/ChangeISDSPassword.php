<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use TomasKulhanek\CzechDataBox\DTO\Response\IResponse;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

/**
 * Class ChangeISDSPassword
 *
 * @Serializer\XmlNamespace(uri="http://isds.czechpoint.cz/v20",prefix="p")
 * @Serializer\XmlRoot(name="p:ChangeISDSPasswordResponse", namespace="http://isds.czechpoint.cz/v20")
 */
class ChangeISDSPassword extends IResponse
{
    use DataBoxStatus;
}
