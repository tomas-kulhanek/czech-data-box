<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Provider;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;
use TomasKulhanek\CzechDataBox\Exception\ConnectionException;
use TomasKulhanek\CzechDataBox\Exception\FileSystemException;
use TomasKulhanek\CzechDataBox\Exception\MissingRequiredField;
use TomasKulhanek\CzechDataBox\Exception\SystemExclusion;

readonly class SymfonyClientProvider implements ClientProviderInterface
{
    public static function create(): self
    {
        return new self(HttpClient::create(), new EndpointProvider());
    }

    public function __construct(
        private HttpClientInterface $client,
        private EndpointProvider $endpointProvider,
        private string $caCertPath = __DIR__ . '/../cacert.pem'
    ) {
    }

    /**
     * @return array<string, string>
     */
    private function getHeaders(Account $account): array
    {
        $headers = [
            'Connection' => 'Keep-Alive',
            'Accept-Encoding' => 'gzip,deflate',
            'Content-Type' => 'text/xml; charset=utf-8',
            'SOAPAction' => '""',
        ];
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
        $requestOptions = [];
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
            $requestOptions['local_cert'] = $publicStream['uri'];
            $requestOptions['local_pk'] = $privateStream['uri'];
            $requestOptions['passphrase'] = $account->getPrivateKeyPassPhrase();
        }

        $requestOptions['headers'] = $this->getHeaders($account);
        $requestOptions['body'] = $xmlBody;
        if (file_exists($this->caCertPath)) {
            $requestOptions['cafile'] = $this->caCertPath;
        }

        try {
            return $this->client->request(
                'POST',
                $this->endpointProvider->getServiceLocation($account, $serviceType),
                $requestOptions
            )->getContent();
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
