<?php declare(strict_types=1);


namespace TomasKulhanek\CzechDataBox;


use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use TomasKulhanek\CzechDataBox\Exception\ConnectionException;
use TomasKulhanek\CzechDataBox\Exception\SystemExclusion;

class Dispatcher implements DispatcherInterface
{

    public const OPERATIONS = 0;
    public const INFO = 1;
    public const SEARCH = 2;
    public const SUPPLEMENTARY = 3;
    public const ACCESS = 5;

    private ClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private StreamFactoryInterface $streamFactory;
    private UriFactoryInterface $uriFactory;

    public function __construct(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        UriFactoryInterface $uriFactory
    )
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
        $this->uriFactory = $uriFactory;
    }

    public function dispatch(Account $account, int $serviceType, string $xmlBody): ResponseInterface
    {
        $request = $this->createRequest($account, $serviceType)
            ->withBody($this->streamFactory->createStream($xmlBody));

        if ($account->usingCertificate()) { //todo
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
            $requestOptions['local_cert'] = stream_get_meta_data($publicCert)['uri'];
            $requestOptions['local_pk'] = stream_get_meta_data($privateKey)['uri'];
            $requestOptions['passphrase'] = $account->getPrivateKeyPassPhrase();
        }

        try {
            $response = $this->httpClient->sendRequest($request);
            if ($response->getStatusCode() === 503) {
                throw new SystemExclusion((string) $response->getStatusCode(), $response->getStatusCode());
            }
            return $response;
        } catch (\Throwable $exception) {
            throw new ConnectionException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }


    private function getServiceDomain(bool $isProduction): string
    {
        if ($isProduction) {
            return 'mojedatovaschranka.cz';
        }
        return 'czebox.cz';
    }

    private function getServiceUrl(int $serviceType): ?string
    {
        if (in_array($serviceType, [self::SUPPLEMENTARY, self::ACCESS], true)) {
            return 'DsManage';
        }
        switch ($serviceType) {
            case self::OPERATIONS:
                return 'dz';
            case self::INFO:
                return 'dx';
            case self::SEARCH:
                return 'df';
        }
        return null;
    }

    private function getServiceLocation(Account $account, int $ServiceType): UriInterface
    {
        $res = 'https://ws1';
        if ($account->getLoginType() !== Account::LOGIN_NAME_PASSWORD) {
            $res .= 'c';
        }

        $res .= '.' . $this->getServiceDomain($account->isProduction()) . '/';

        switch ($account->getLoginType()) {
            case Account::LOGIN_CERT_LOGIN_NAME_PASSWORD:
                $res .= 'certds/';
                break;
            case Account::LOGIN_SPIS_CERT:
                $res .= 'cert/';
                break;
            case Account::LOGIN_HOSTED_SPIS:
                $res .= 'hspis/';
                break;
        }

        $res .= 'DS/' . $this->getServiceUrl($ServiceType);

        return $this->uriFactory->createUri($res);
    }

    private function createRequest(Account $account, int $serviceType): RequestInterface
    {
        $request = $this->requestFactory
            ->createRequest('POST', $this->getServiceLocation($account, $serviceType))
            ->withHeader('Connection', 'Keep-Alive')
            ->withHeader('Accept-Encoding', 'gzip,deflate')
            ->withHeader('Content-Type', 'text/xml; charset=utf-8')
            ->withHeader('SOAPAction', '""');

        switch ($account->getLoginType()) {
            case Account::LOGIN_HOSTED_SPIS:
                $header = sprintf('Basic %s', base64_encode((string) $account->getDataBoxId()));
                $request = $request->withHeader('Authorization', $header);
                break;
            case Account::LOGIN_NAME_PASSWORD:
            case Account::LOGIN_CERT_LOGIN_NAME_PASSWORD:
                $header = sprintf('Basic %s', base64_encode($account->getLoginName() . ':' . $account->getPassword()));
                $request = $request->withHeader('Authorization', $header);
                break;
        }

        return $request;
    }
}