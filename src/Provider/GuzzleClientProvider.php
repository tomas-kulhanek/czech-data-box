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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
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
        private RequestOptionsFactory $requestOptionsFactory = new RequestOptionsFactory(),
        private ResponseSizeLimit $responseSizeLimit = new ResponseSizeLimit()
    ) {
        $this->caCertPath = $caCertPath ?? CaBundle::getSystemCaRootBundlePath();
    }

    private static function describe(Throwable $exception): string
    {
        return sprintf('%s: %s', $exception::class, $exception->getMessage());
    }

    /**
     * @return iterable<string>
     */
    private static function readChunks(StreamInterface $body, int $chunkSize): iterable
    {
        while (!$body->eof()) {
            $chunk = $body->read($chunkSize);
            if ($chunk === '') {
                break;
            }
            yield $chunk;
        }
    }

    private static function readBody(ResponseInterface $response, ResponseSizeLimit $limit): string
    {
        $limit->rejectAnnouncedSize($response->getHeaderLine('Content-Length'));
        $body = $response->getBody();
        try {
            return $limit->collect(self::readChunks($body, $limit->getChunkSize()));
        } catch (ConnectionException $exception) {
            $body->close();

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
            $requestOptions[RequestOptions::AUTH] = $authentication;
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
            $requestOptions[RequestOptions::CERT] = [$publicStream['uri'], $account->getPrivateKeyPassPhrase()];
            $requestOptions[RequestOptions::SSL_KEY] = [$privateStream['uri'], $account->getPrivateKeyPassPhrase()];
        }

        $requestOptions[RequestOptions::HEADERS] = $this->requestOptionsFactory->createHeaders($serviceType);
        $requestOptions[RequestOptions::BODY] = $xmlBody;
        $requestOptions[RequestOptions::ON_HEADERS] = static function (ResponseInterface $response) use ($limit): void {
            $limit->rejectAnnouncedSize($response->getHeaderLine('Content-Length'));
        };
        if (file_exists($this->caCertPath)) {
            $requestOptions[RequestOptions::VERIFY] = $this->caCertPath;
        }

        try {
            return self::readBody(
                $this->client->request(
                    'POST',
                    $this->endpointProvider->getServiceLocation($account, $serviceType),
                    $requestOptions
                ),
                $limit
            );
        } catch (BadResponseException $exception) {
            $response = $exception->getResponse();
            $statusCode = $response->getStatusCode();
            if ($statusCode === 503) {
                throw new SystemExclusion(self::describe($exception), $statusCode);
            }
            $body = self::readBody($response, $limit);
            if ($body !== '') {
                return $body;
            }

            throw new ConnectionException(self::describe($exception), $statusCode);
        } catch (ConnectionException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            $cause = $exception->getPrevious();
            if ($cause instanceof ConnectionException) {
                throw $cause;
            }

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
