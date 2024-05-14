<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Provider;

use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;

class EndpointProvider
{
    private function getServiceDomain(bool $isProduction): string
    {
        if ($isProduction) {
            return 'mojedatovaschranka.cz';
        }
        return 'czebox.cz';
    }

    private function getServiceUrl(ServiceTypeEnum $serviceType): string
    {
        return match ($serviceType) {
            ServiceTypeEnum::OPERATIONS => 'dz',
            ServiceTypeEnum::INFO => 'dx',
            ServiceTypeEnum::SEARCH => 'df',
            ServiceTypeEnum::SUPPLEMENTARY, ServiceTypeEnum::ACCESS => 'DsManage'
        };
    }

    public function getServiceLocation(Account $account, ServiceTypeEnum $ServiceType): string
    {
        $res = 'https://ws1';
        if ($account->getLoginType() !== LoginTypeEnum::NAME_PASSWORD) {
            $res .= 'c';
        }

        $res .= '.' . $this->getServiceDomain($account->isProduction()) . '/';

        $res .= match ($account->getLoginType()) {
            LoginTypeEnum::CERT_LOGIN_NAME_PASSWORD => 'certds/',
            LoginTypeEnum::SPIS_CERT => 'cert/',
            LoginTypeEnum::HOSTED_SPIS => 'hspis/',
            default => '',
        };

        return $res . 'DS/' . $this->getServiceUrl($ServiceType);
    }
}
