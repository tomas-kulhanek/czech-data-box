<?php

declare(strict_types=1);


namespace TomasKulhanek\CzechDataBox;

use Psr\Http\Message\ResponseInterface;

interface DispatcherInterface
{
    public function dispatch(Account $account, int $serviceType, string $xmlBody): ResponseInterface;
}
