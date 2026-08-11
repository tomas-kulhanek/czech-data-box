<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Provider;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;
use TomasKulhanek\CzechDataBox\Exception\ConnectionException;
use TomasKulhanek\CzechDataBox\Exception\SystemExclusion;

readonly class Psr18ClientProvider implements ClientProviderInterface
{
    private EndpointProviderInterface $endpointProvider;

    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
        ?EndpointProviderInterface $endpointProvider = null
    ) {
        $this->endpointProvider = $endpointProvider ?? new EndpointProvider();
    }

    /**
     * @return array<string, string>
     */
    private function getHeaders(Account $account, ServiceTypeEnum $serviceType): array
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
        switch ($account->getLoginType()) {
            case LoginTypeEnum::HOSTED_SPIS:
                $headers['Authorization'] = sprintf('Basic %s', base64_encode((string) $account->getDataBoxId()));
                break;
            case LoginTypeEnum::NAME_PASSWORD:
            case LoginTypeEnum::CERT_LOGIN_NAME_PASSWORD:
                $headers['Authorization'] = sprintf('Basic %s', base64_encode($account->getLoginName() . ':' . $account->getPassword()));
                break;
        }

        return $headers;
    }

    public function sendRequest(Account $account, ServiceTypeEnum $serviceType, string $xmlBody): string
    {
        if ($account->usingCertificate() && (!empty($account->getPublicKey()) || !empty($account->getPrivateKey()))) {
            throw new ConnectionException(
                'PSR-18 has no transport options, so the client certificate stored in the Account cannot be used.'
                . ' Configure mTLS (client certificate and private key) on the underlying PSR-18 client'
                . ' and leave the certificate out of the Account.'
            );
        }

        $request = $this->requestFactory
            ->createRequest('POST', $this->endpointProvider->getServiceLocation($account, $serviceType))
            ->withBody($this->streamFactory->createStream($xmlBody));
        foreach ($this->getHeaders($account, $serviceType) as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $exception) {
            throw new ConnectionException($exception->getMessage(), $exception->getCode(), $exception);
        }

        if ($response->getStatusCode() === 503) {
            throw new SystemExclusion($response->getReasonPhrase(), $response->getStatusCode());
        }

        $body = (string) $response->getBody();
        if ($response->getStatusCode() >= 400 && $body === '') {
            throw new ConnectionException($response->getReasonPhrase(), $response->getStatusCode());
        }

        return $body;
    }
}
