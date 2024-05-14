<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Exception\ConnectionException;
use TomasKulhanek\CzechDataBox\Exception\FileSystemException;
use TomasKulhanek\CzechDataBox\Exception\MissingRequiredField;
use TomasKulhanek\CzechDataBox\Exception\SystemExclusion;

class GuzzleClientProvider implements ClientProviderInterface
{
    private Client $client;
    private EndpointProvider $endpointProvider;

    public static function create(): self
    {
        return new self(new Client(), new EndpointProvider());
    }

    public function __construct(Client $client, EndpointProvider $endpointProvider, private string $caCertPath = __DIR__ . '/../cacert.pem')
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
     * @return array<int, string|null>
     */
    private function getAuthentication(Account $account): array
    {
        return match ($account->getLoginType()) {
            Account::LOGIN_HOSTED_SPIS => [$account->getDataBoxId(), null],
            Account::LOGIN_NAME_PASSWORD, Account::LOGIN_CERT_LOGIN_NAME_PASSWORD => [$account->getLoginName(), $account->getPassword()],
            default => [],
        };
    }

    public function sendRequest(Account $account, int $serviceType, string $xmlBody): string
    {
        $requestOptions = [
            RequestOptions::AUTH => $this->getAuthentication($account),
        ];
        if ($account->usingCertificate()) {
            if (empty($account->getPublicKey()) || empty($account->getPrivateKey())) {
                throw new MissingRequiredField('Missing PEM data');
            }
            $publicCert = tmpfile();
            if (!$publicCert) {
                throw new FileSystemException('Failed to create temp file for public certificate.');
            }
            $privateKey = tmpfile();
            if (!$privateKey) {
                fclose($publicCert);
                throw new FileSystemException('Failed to create temp file for private key.');
            }
            fwrite($publicCert, $account->getPublicKey());
            fwrite($privateKey, $account->getPrivateKey());

            $publicStream = stream_get_meta_data($publicCert);
            if (!array_key_exists('uri', $publicStream)) {
                throw new \LogicException('Failed to get stream metadata of public certificate');
            }
            $privateStream = stream_get_meta_data($privateKey);
            if (!array_key_exists('uri', $privateStream)) {
                throw new \LogicException('Failed to get stream metadata of private certificate');
            }
            $requestOptions[RequestOptions::CERT] = [$publicStream['uri'], $account->getPrivateKeyPassPhrase()];
            $requestOptions[RequestOptions::SSL_KEY] = [$privateStream['uri'], $account->getPrivateKeyPassPhrase()];
        }

        $requestOptions[RequestOptions::HEADERS] = $this->getHeaders();
        $requestOptions[RequestOptions::BODY] = $xmlBody;
        if (file_exists($this->caCertPath)) {
            $requestOptions['cafile'] = $this->caCertPath;
        }

        try {
            return $this->client->request(
                'POST',
                $this->endpointProvider->getServiceLocation($account, $serviceType),
                $requestOptions
            )->getBody()->getContents();
        } catch (\Throwable $exception) {
            /** @var TransportExceptionInterface $exception */
            if (is_a($exception, TransportExceptionInterface::class) && $exception->getCode() === 503) {
                throw new SystemExclusion($exception->getMessage(), $exception->getCode(), $exception);
            }

            throw new ConnectionException($exception->getMessage(), $exception->getCode(), $exception);
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
