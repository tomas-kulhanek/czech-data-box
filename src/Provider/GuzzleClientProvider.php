<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Provider;

use LogicException;
use Throwable;
use Composer\CaBundle\CaBundle;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;
use TomasKulhanek\CzechDataBox\Exception\ConnectionException;
use TomasKulhanek\CzechDataBox\Exception\FileSystemException;
use TomasKulhanek\CzechDataBox\Exception\MissingRequiredField;
use TomasKulhanek\CzechDataBox\Exception\SystemExclusion;

readonly class GuzzleClientProvider implements ClientProviderInterface
{
    private string $caCertPath;

    public static function create(?EndpointProviderInterface $endpointProvider = null): self
    {
        return new self(new Client(), $endpointProvider ?? new EndpointProvider());
    }

    public function __construct(
        private ClientInterface $client,
        private EndpointProviderInterface $endpointProvider,
        ?string $caCertPath = null,
        private RequestOptionsFactory $requestOptionsFactory = new RequestOptionsFactory()
    ) {
        $this->caCertPath = $caCertPath ?? CaBundle::getSystemCaRootBundlePath();
    }

    public function sendRequest(Account $account, ServiceTypeEnum $serviceType, string $xmlBody): string
    {
        $requestOptions = [];
        $authentication = $this->requestOptionsFactory->createBasicAuthentication($account);
        if ($authentication !== null) {
            $requestOptions[RequestOptions::AUTH] = $authentication;
        }

        $publicCert = null;
        $privateKey = null;
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
                throw new LogicException('Failed to get stream metadata of public certificate');
            }
            $privateStream = stream_get_meta_data($privateKey);
            if (!array_key_exists('uri', $privateStream)) {
                throw new LogicException('Failed to get stream metadata of private certificate');
            }
            $requestOptions[RequestOptions::CERT] = [$publicStream['uri'], $account->getPrivateKeyPassPhrase()];
            $requestOptions[RequestOptions::SSL_KEY] = [$privateStream['uri'], $account->getPrivateKeyPassPhrase()];
        }

        $requestOptions[RequestOptions::HEADERS] = $this->requestOptionsFactory->createHeaders($serviceType);
        $requestOptions[RequestOptions::BODY] = $xmlBody;
        if (file_exists($this->caCertPath)) {
            $requestOptions[RequestOptions::VERIFY] = $this->caCertPath;
        }

        try {
            return $this->client->request(
                'POST',
                $this->endpointProvider->getServiceLocation($account, $serviceType),
                $requestOptions
            )->getBody()->getContents();
        } catch (BadResponseException $exception) {
            $response = $exception->getResponse();
            $statusCode = $response->getStatusCode();
            if ($statusCode === 503) {
                throw new SystemExclusion($exception->getMessage(), $statusCode, $exception);
            }
            $body = (string) $response->getBody();
            if ($body !== '') {
                return $body;
            }

            throw new ConnectionException($exception->getMessage(), $statusCode, $exception);
        } catch (Throwable $exception) {
            throw new ConnectionException($exception->getMessage(), $exception->getCode(), $exception);
        } finally {
            if (is_resource($publicCert)) {
                fclose($publicCert);
            }
            if (is_resource($privateKey)) {
                fclose($privateKey);
            }
        }
    }
}
