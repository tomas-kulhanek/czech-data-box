<?php

declare(strict_types=1);


namespace TomasKulhanek\CzechDataBox\Provider;

use TomasKulhanek\CzechDataBox\Account;

interface ClientProviderInterface
{
	public function sendRequest(Account $account, int $serviceType, string $xmlBody): string;
}
