<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Provider;

use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;

interface ClientProviderInterface
{
    public function sendRequest(Account $account, ServiceTypeEnum $serviceType, string $xmlBody): string;
}
