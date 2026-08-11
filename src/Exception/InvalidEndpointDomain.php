<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Exception;

use Exception;

class InvalidEndpointDomain extends Exception implements CzechDataBoxException
{
    public function __construct(string $domain)
    {
        parent::__construct(sprintf('The endpoint domain \'%s\' is not a valid host name.', $domain));
    }
}
