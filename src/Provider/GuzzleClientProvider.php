<?php

declare(strict_types=1);


namespace TomasKulhanek\CzechDataBox\Provider;

use GuzzleHttp\Client;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Exception;

class GuzzleClientProvider implements ClientProviderInterface
{
    private Client $client;
    private EndpointProvider $endpointProvider;

    public static function create(): self
    {
        return new self(new Client(), new EndpointProvider());
    }

    public function __construct(Client $client, EndpointProvider $endpointProvider)
    {
        $this->client = $client;
        $this->endpointProvider = $endpointProvider;
    }

    /**
     * @return array<string, string>
     */
    private function getHeaders(): array
    {
        $headers = [
            'Connection' => 'Keep-Alive',
            'Accept-Encoding' => 'gzip,deflate',
            'Content-Type' => 'text/xml; charset=utf-8',
            'SOAPAction' => '""',
        ];

        return $headers;
    }

    /**
     * @param Account $account
     * @return array<int, string|null>
     */
    private function getAuthentication(Account $account): array
    {
        switch ($account->getLoginType()) {
            case Account::LOGIN_HOSTED_SPIS:
                return [$account->getDataBoxId(), null];
            case Account::LOGIN_NAME_PASSWORD:
            case Account::LOGIN_CERT_LOGIN_NAME_PASSWORD:
                return [$account->getLoginName(), $account->getPassword()];
        }
        return [];
    }

    public function sendRequest(Account $account, int $serviceType, string $xmlBody): string
    {
        $requestOptions = [
            \GuzzleHttp\RequestOptions::AUTH => $this->getAuthentication($account),
        ];
        if ($account->usingCertificate()) {
            if (empty($account->getPublicKey()) || empty($account->getPrivateKey())) {
                throw new Exception\MissingRequiredField('Missing PEM data');
            }
            $publicCert = tmpfile();
            if (!$publicCert) {
                throw new Exception\FileSystemException('Failed to create temp file for public certificate.');
            }
            $privateKey = tmpfile();
            if (!$privateKey) {
                fclose($publicCert);
                throw new Exception\FileSystemException('Failed to create temp file for private key.');
            }
            fwrite($publicCert, $account->getPublicKey());
            fwrite($privateKey, $account->getPrivateKey());
            $requestOptions[\GuzzleHttp\RequestOptions::CERT] = [stream_get_meta_data($publicCert)['uri'], $account->getPrivateKeyPassPhrase()];
            $requestOptions[\GuzzleHttp\RequestOptions::SSL_KEY] = [stream_get_meta_data($privateKey)['uri'], $account->getPrivateKeyPassPhrase()];
        }

        $requestOptions[\GuzzleHttp\RequestOptions::HEADERS] = $this->getHeaders();
        $requestOptions[\GuzzleHttp\RequestOptions::BODY] = $xmlBody;

        try {
            return $this->client->request(
                'POST',
                $this->endpointProvider->getServiceLocation($account, $serviceType),
                $requestOptions
            )->getBody()->getContents();
        } catch (\Throwable $exception) {
            /** @var TransportExceptionInterface $exception */
            if (is_a($exception, TransportExceptionInterface::class) && $exception->getCode() === 503) {
                throw new Exception\SystemExclusion($exception->getMessage(), $exception->getCode(), $exception);
            }

            throw new Exception\ConnectionException($exception->getMessage(), $exception->getCode(), $exception);
        } finally {
            if ($account->usingCertificate()) {
                if (isset($publicCert) && is_resource($publicCert)) {
                    fclose($publicCert);
                }
                if (isset($privateKey) && is_resource($privateKey)) {
                    fclose($privateKey);
                }
            }
        }
    }
}
