<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Provider;

use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;

interface EndpointProviderInterface
{
    public function getServiceLocation(Account $account, ServiceTypeEnum $serviceType): string;
}
