<?php

declare(strict_types=1);


namespace TomasKulhanek\CzechDataBox\Provider;

use TomasKulhanek\CzechDataBox\Account;

class EndpointProvider
{
	final public const OPERATIONS = 0;
	final public const INFO = 1;
	final public const SEARCH = 2;
	final public const SUPPLEMENTARY = 3;
	final public const ACCESS = 5;

	private function getServiceDomain(bool $isProduction): string
	{
		if ($isProduction) {
			return 'mojedatovaschranka.cz';
		}
		return 'czebox.cz';
	}

	private function getServiceUrl(int $serviceType): ?string
	{
		if (in_array($serviceType, [self::SUPPLEMENTARY, self::ACCESS], true)) {
			return 'DsManage';
		}
		return match ($serviceType) {
			self::OPERATIONS => 'dz',
			self::INFO => 'dx',
			self::SEARCH => 'df',
			default => null,
		};
	}

	public function getServiceLocation(Account $account, int $ServiceType): string
	{
		$res = 'https://ws1';
		if ($account->getLoginType() !== Account::LOGIN_NAME_PASSWORD) {
			$res .= 'c';
		}

		$res .= '.' . $this->getServiceDomain($account->isProduction()) . '/';

		switch ($account->getLoginType()) {
			case Account::LOGIN_CERT_LOGIN_NAME_PASSWORD:
				$res .= 'certds/';
				break;
			case Account::LOGIN_SPIS_CERT:
				$res .= 'cert/';
				break;
			case Account::LOGIN_HOSTED_SPIS:
				$res .= 'hspis/';
				break;
		}

		$res .= 'DS/' . $this->getServiceUrl($ServiceType);

		return $res;
	}
}
