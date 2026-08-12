<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Provider;

use LogicException;
use Throwable;
use Composer\CaBundle\CaBundle;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;
use TomasKulhanek\CzechDataBox\Exception\ConnectionException;
use TomasKulhanek\CzechDataBox\Exception\FileSystemException;
use TomasKulhanek\CzechDataBox\Exception\MissingRequiredField;
use TomasKulhanek\CzechDataBox\Exception\SystemExclusion;

readonly class SymfonyClientProvider implements ClientProviderInterface
{
    private string $caCertPath;

    public static function create(?EndpointProviderInterface $endpointProvider = null): self
    {
        return new self(HttpClient::create(), $endpointProvider ?? new EndpointProvider());
    }

    public function __construct(
        private HttpClientInterface $client,
        private EndpointProviderInterface $endpointProvider,
        ?string $caCertPath = null,
        private RequestOptionsFactory $requestOptionsFactory = new RequestOptionsFactory(),
        private ResponseSizeLimit $responseSizeLimit = new ResponseSizeLimit()
    ) {
        $this->caCertPath = $caCertPath ?? CaBundle::getSystemCaRootBundlePath();
    }

    private static function describe(Throwable $exception): string
    {
        return sprintf('%s: %s', $exception::class, $exception->getMessage());
    }

    private static function announcedSize(ResponseInterface $response): ?string
    {
        $contentLength = $response->getHeaders(false)['content-length'][0] ?? null;

        return is_string($contentLength) ? $contentLength : null;
    }

    /**
     * @return iterable<string>
     */
    private static function readChunks(ResponseStreamInterface $stream): iterable
    {
        foreach ($stream as $chunk) {
            if ($chunk->isTimeout()) {
                throw new ConnectionException('The connection timed out while reading the response.');
            }
            if ($chunk->isFirst() || $chunk->isLast()) {
                continue;
            }
            yield $chunk->getContent();
        }
    }

    private function readBody(ResponseInterface $response, ResponseSizeLimit $limit): string
    {
        try {
            $limit->rejectAnnouncedSize(self::announcedSize($response));

            return $limit->collect(self::readChunks($this->client->stream($response)));
        } catch (ConnectionException $exception) {
            $response->cancel();

            throw $exception;
        }
    }

    public function sendRequest(
        Account $account,
        ServiceTypeEnum $serviceType,
        string $xmlBody,
        ?int $maxResponseSize = null
    ): string {
        $limit = $this->responseSizeLimit->withMaxBytes($maxResponseSize);
        $requestOptions = [];
        $authentication = $this->requestOptionsFactory->createBasicAuthentication($account);
        if ($authentication !== null) {
            $requestOptions['auth_basic'] = $authentication;
        }

        $publicCert = null;
        $privateKey = null;
        if ($account->usingCertificate()) {
            $publicKeyPem = $account->getPublicKey();
            if ($publicKeyPem === null || $publicKeyPem === '') {
                throw new MissingRequiredField('publicKey');
            }
            $privateKeyPem = $account->getPrivateKey();
            if ($privateKeyPem === null || $privateKeyPem === '') {
                throw new MissingRequiredField('privateKey');
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
            fwrite($publicCert, $publicKeyPem);
            fwrite($privateKey, $privateKeyPem);

            $publicStream = stream_get_meta_data($publicCert);
            if (!array_key_exists('uri', $publicStream)) {
                throw new LogicException('Failed to get stream metadata of public certificate');
            }
            $privateStream = stream_get_meta_data($privateKey);
            if (!array_key_exists('uri', $privateStream)) {
                throw new LogicException('Failed to get stream metadata of private certificate');
            }
            $requestOptions['local_cert'] = $publicStream['uri'];
            $requestOptions['local_pk'] = $privateStream['uri'];
            $requestOptions['passphrase'] = $account->getPrivateKeyPassPhrase();
        }

        $requestOptions['headers'] = $this->requestOptionsFactory->createHeaders($serviceType);
        $requestOptions['body'] = $xmlBody;
        $requestOptions['buffer'] = false;
        if (is_dir($this->caCertPath)) {
            $requestOptions['capath'] = $this->caCertPath;
        } elseif (file_exists($this->caCertPath)) {
            $requestOptions['cafile'] = $this->caCertPath;
        }

        try {
            $response = $this->client->request(
                'POST',
                $this->endpointProvider->getServiceLocation($account, $serviceType),
                $requestOptions
            );
            $statusCode = $response->getStatusCode();
            if ($statusCode === 503) {
                $response->cancel();

                throw new SystemExclusion(sprintf('The server responded with HTTP %d.', $statusCode), $statusCode);
            }
            $content = $this->readBody($response, $limit);
            if ($statusCode >= 400 && $content === '') {
                throw new ConnectionException(sprintf('The server responded with HTTP %d and an empty body.', $statusCode), $statusCode);
            }

            return $content;
        } catch (TransportExceptionInterface $exception) {
            throw new ConnectionException(self::describe($exception), $exception->getCode());
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
