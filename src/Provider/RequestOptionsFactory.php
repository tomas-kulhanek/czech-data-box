<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Provider;

use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;
use TomasKulhanek\CzechDataBox\Exception\MissingRequiredField;

final readonly class RequestOptionsFactory
{
    /**
     * @return array<string, non-empty-string>
     */
    public function createHeaders(ServiceTypeEnum $serviceType): array
    {
        $headers = [
            'Connection' => 'Keep-Alive',
            'Accept-Encoding' => 'gzip,deflate',
        ];
        if ($serviceType->usesSoap12()) {
            $headers['Content-Type'] = 'application/soap+xml; charset=utf-8';
        } else {
            $headers['Content-Type'] = 'text/xml; charset=utf-8';
            $headers['SOAPAction'] = '""';
        }

        return $headers;
    }

    /**
     * @return array{0: string, 1: string}|null
     *
     * @throws MissingRequiredField
     */
    public function createBasicAuthentication(Account $account): ?array
    {
        switch ($account->getLoginType()) {
            case LoginTypeEnum::HOSTED_SPIS:
                $dataBoxId = $account->getDataBoxId();
                if ($dataBoxId === null) {
                    throw new MissingRequiredField('Missing data box ID');
                }

                return [$dataBoxId, ''];
            case LoginTypeEnum::NAME_PASSWORD:
            case LoginTypeEnum::CERT_LOGIN_NAME_PASSWORD:
                $loginName = $account->getLoginName();
                $password = $account->getPassword();
                if ($loginName === null || $password === null) {
                    throw new MissingRequiredField('Missing login name or password');
                }

                return [$loginName, $password];
            default:
                return null;
        }
    }
}
