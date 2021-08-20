<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Connector;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use TomasKulhanek\CzechDataBox\Exception\ConnectionException;
use TomasKulhanek\CzechDataBox\Exception\FileSystemException;
use TomasKulhanek\CzechDataBox\Exception\MissingRequiredField;
use TomasKulhanek\CzechDataBox\Exception\SystemExclusion;
use TomasKulhanek\CzechDataBox\IRequest;
use TomasKulhanek\CzechDataBox\IResponse;
use JMS\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

abstract class Connector
{

    public const OPERATIONS = 0;
    public const INFO = 1;
    public const SEARCH = 2;
    public const SUPPLEMENTARY = 3;
    public const ACCESS = 5;
    
    private HttpClientInterface $httpClient;

    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer, HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
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

    private function getServiceLocation(Account $account, int $ServiceType): string
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

        return $res;
    }

    private function getXmlDocument(?string $xmlContent = null): DOMDocument
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        if ($xmlContent !== null) {
            $document->loadXML($xmlContent);
            return $document;
        }
        $document->loadXML('<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"><SOAP-ENV:Header/><SOAP-ENV:Body></SOAP-ENV:Body></SOAP-ENV:Envelope>');
        return $document;
    }

    private function getValueByXpath(DOMDocument $document, string $xpath): ?string
    {
        $domXpath = new DOMXPath($document);
        $result = null;
        $res = $domXpath->evaluate($xpath);
        if ($res instanceof DOMNodeList) {
            foreach ($res as $node) {
                if ($node instanceof DOMElement || $node instanceof DOMDocument) {
                    $nodeValue = null;
                    $children = $node->childNodes;
                    foreach ($children as $child) {
                        $nodeValue .= $document->saveXML($child);
                    }
                } else {
                    $nodeValue = $node->nodeValue;
                }
                $result .= $nodeValue;
            }
        }
        return $result;
    }


    /**
     * @template T of IResponse
     * @param Account $account
     * @param int $serviceType
     * @param IRequest $request
     * @param string $responseClass
     * @return IResponse
     * @throws ConnectionException
     * @throws FileSystemException
     * @throws MissingRequiredField
     * @throws SystemExclusion
     * @phpstan-param class-string<T> $responseClass
     * @phpstan-return T
     */
    protected function send(Account $account, int $serviceType, IRequest $request, string $responseClass): IResponse
    {
        $location = $this->getLocation($account, $serviceType);
        if (!is_subclass_of($responseClass, IResponse::class)) {
            throw new ConnectionException();
        }

        $request = $this->serializer->serialize($request, 'xml');
        $request = $this->getXmlDocument($request);

        $requestDocument = $this->getXmlDocument();
        $requestDocumentXpath = new DOMXPath($requestDocument);
        if (empty($requestDocument->documentElement)) {
            throw new ConnectionException();
        }
        $bodyNode = $requestDocumentXpath->evaluate('//' . $requestDocument->documentElement->prefix . ':Body');
        $new = $bodyNode[0]->ownerDocument->importNode($request->documentElement, true);
        if ($bodyNode[0]->nextSibling) {
            $bodyNode[0]->insertBefore($new, $bodyNode[0]->nextSibling);
        } else {
            $bodyNode[0]->appendChild($new);
        }

        $requestOptions = [
            'headers' => [
                'Connection' => 'Keep-Alive',
                'Accept-Encoding' => 'gzip,deflate',
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => '""',
            ],
            'body' => $requestDocument->saveXml(),
        ];
        switch ($account->getLoginType()) {
            case Account::LOGIN_HOSTED_SPIS:
                $requestOptions['auth_basic'] = $account->getDataBoxId();
                break;
            case Account::LOGIN_NAME_PASSWORD:
            case Account::LOGIN_CERT_LOGIN_NAME_PASSWORD:
                $requestOptions['auth_basic'] = $account->getLoginName() . ':' . $account->getPassword();
                break;
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
            $requestOptions['local_cert'] = stream_get_meta_data($publicCert)['uri'];
            $requestOptions['local_pk'] = stream_get_meta_data($privateKey)['uri'];
            $requestOptions['passphrase'] = $account->getPrivateKeyPassPhrase();
        }

        try {
            $response = $this->httpClient->request('POST', $location, $requestOptions);
            $response = $response->getContent();
            $soapResponse = $this->getXmlDocument($response);
            if (empty($soapResponse->documentElement)) {
                throw new ConnectionException('The response is empty');
            }
            $response = $this->getValueByXpath($soapResponse, '//' . $soapResponse->documentElement->prefix . ':Body');
            $soapResponse = null;
            $dom = $this->getXmlDocument($response);
            if (empty($dom->documentElement)) {
                throw new ConnectionException('The response is empty');
            }
            $prefix = $dom->documentElement->prefix;
            if ($prefix !== 'p') {
                $dom->documentElement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:p', 'http://isds.czechpoint.cz/v20');
                /** @var string $response */
                $response = $dom->saveXML();
                $regex = ['/(<|<\/)' . $prefix . ':(\w*)(\s|>|\/>)/'];
                $replace = ['\1p:\2\3'];
                $response = preg_replace($regex, $replace, $response);
            }
            if (empty($response)) {
                throw new ConnectionException('The response is empty');
            }

        } catch (Throwable $exception) {
            /** @var TransportExceptionInterface $exception */
            if (is_a($exception, TransportExceptionInterface::class) && $exception->getCode() === 503) {

                throw new SystemExclusion($exception->getMessage(), $exception->getCode(), $exception);
            }

            throw new ConnectionException($exception->getMessage(), $exception->getCode(), $exception);
        } finally {
            if ($account->usingCertificate()) {
                if (is_resource($publicCert)) {
                    fclose($publicCert);
                }
                if (is_resource($privateKey)) {
                    fclose($privateKey);
                }
            }
        }
        return $this->serializer->deserialize($response, $responseClass, 'xml');
    }

    protected function getLocation(Account $account, int $serviceType): string
    {
        return $this->getServiceLocation($account, $serviceType);
    }

}
