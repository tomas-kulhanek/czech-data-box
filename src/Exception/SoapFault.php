<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Exception;

final class SoapFault extends ConnectionException
{
    public function __construct(
        public readonly string $faultCode,
        public readonly string $faultString
    ) {
        parent::__construct(sprintf('The ISDS returned a SOAP fault %s: %s', $faultCode, $faultString));
    }
}
