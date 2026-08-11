<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Provider;

use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;
use TomasKulhanek\CzechDataBox\Exception\MissingRequiredField;

/**
 * Request composition shared by the {@see ClientProviderInterface} implementations, so that
 * switching the HTTP client never changes the headers nor the credentials sent to ISDS.
 */
trait ClientRequestTrait
{
    /**
     * @return array<string, non-empty-string>
     */
    private function getHeaders(ServiceTypeEnum $serviceType): array
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
     * Credentials for HTTP Basic authentication as a user and password pair, or null when the
     * account is authenticated by the client certificate alone. Incomplete credentials are
     * rejected here, before any request is sent - ISDS rate limits failed login attempts.
     *
     * @return array{0: string, 1: string}|null
     */
    private function getAuthentication(Account $account): ?array
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
