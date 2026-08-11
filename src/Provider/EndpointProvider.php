<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Provider;

use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;
use TomasKulhanek\CzechDataBox\Exception\InvalidEndpointDomain;

class EndpointProvider implements EndpointProviderInterface
{
    public const PRODUCTION_DOMAIN = 'datovka.gov.cz';
    public const TEST_DOMAIN = 'datovka-test.gov.cz';

    /**
     * Bare host name: dot separated DNS labels, no scheme, userinfo, port, path or control characters.
     */
    private const DOMAIN_PATTERN = '/^(?=.{1,253}\z)[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?(?:\.[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)*\z/i';

    /**
     * @param string $domain Bare host name of the ISDS environment, e.g. 'datovka.cms2.cz' for KIVS.
     *                       It must come from trusted configuration, never from user input - the
     *                       resulting URL receives the Basic Auth credentials and the client certificate.
     *
     * @throws InvalidEndpointDomain When the domain is not a bare host name.
     */
    public function __construct(
        private readonly string $domain = self::PRODUCTION_DOMAIN
    ) {
        if (preg_match(self::DOMAIN_PATTERN, $domain) !== 1) {
            throw new InvalidEndpointDomain($domain);
        }
    }

    public static function production(): self
    {
        return new self(self::PRODUCTION_DOMAIN);
    }

    public static function test(): self
    {
        return new self(self::TEST_DOMAIN);
    }

    private function getServiceUrl(ServiceTypeEnum $serviceType): string
    {
        return match ($serviceType) {
            ServiceTypeEnum::OPERATIONS => 'dz',
            ServiceTypeEnum::INFO => 'dx',
            ServiceTypeEnum::SEARCH => 'df',
            ServiceTypeEnum::ACCESS => 'DsManage',
            ServiceTypeEnum::VODZ => 'vodz',
            ServiceTypeEnum::ARCHIVE => 'arch',
        };
    }

    public function getServiceLocation(Account $account, ServiceTypeEnum $serviceType): string
    {
        $res = 'https://ws' . ($serviceType->usesSoap12() ? '2' : '1');
        if ($account->getLoginType() !== LoginTypeEnum::NAME_PASSWORD) {
            $res .= 'c';
        }

        $res .= '.' . $this->domain . '/';

        $res .= match ($account->getLoginType()) {
            LoginTypeEnum::CERT_LOGIN_NAME_PASSWORD => 'certds/',
            LoginTypeEnum::SPIS_CERT => 'cert/',
            LoginTypeEnum::HOSTED_SPIS => 'hspis/',
            default => '',
        };

        return $res . 'DS/' . $this->getServiceUrl($serviceType);
    }
}
