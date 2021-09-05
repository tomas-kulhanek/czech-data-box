<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Dispatcher;

class DispatcherTest extends TestCase
{
    use GeneratePkcs;
    private const TEST_PASS_PHRASE = 'isds';

    public function testInfoServices(): void
    {
        $account = new Account();
        $account->setProduction(false);
        $account->setLoginType(Account::LOGIN_NAME_PASSWORD);

        $xmlBody = 'XMLDATA';

        $clientInterface = $this->createClientMock();
        $requestFactory = $this->createRequestFactoryMock();
        $streamFactory = $this->createStreamFactoryMock();
        $uriFactory = $this->createUriFactoryMock();

        $streamInterface = $this->createMock(StreamInterface::class);
        $streamFactory->expects($this->once())
            ->method('createStream')
            ->with($xmlBody)
            ->willReturn($streamInterface);

        $requestMock = $this->createMock(RequestInterface::class);

        $map = [
            ['Connection', 'Keep-Alive', $requestMock],
            ['Accept-Encoding', 'gzip,deflate', $requestMock],
            ['Content-Type', 'text/xml; charset=utf-8', $requestMock],
            ['SOAPAction', '""', $requestMock],
            ['Authorization', sprintf('Basic %s', base64_encode($account->getLoginName() . ':' . $account->getPassword())), $requestMock],
        ];
        $requestMock
            ->expects($this->exactly(5))
            ->method('withHeader')
            ->will($this->returnValueMap($map));

        $requestMock
            ->expects($this->once())
            ->method('withBody')
            ->with($streamInterface)
            ->willReturn($requestMock);

        $uriMock = $this->createMock(UriInterface::class);
        $uriFactory->expects($this->once())
            ->method('createUri')
            ->with(...['uri' => 'https://ws1.czebox.cz/DS/dx'])
            ->willReturn($uriMock);

        $requestFactory
            ->expects($this->once())
            ->method('createRequest')
            ->with(...['method' => 'POST', 'uri' => $uriMock])
            ->willReturn($requestMock);

        $clientInterface->expects($this->once())
            ->method('sendRequest')
            ->with($requestMock);

        $dispatcher = new Dispatcher(
            $clientInterface,
            $requestFactory,
            $streamFactory,
            $uriFactory
        );
        $dispatcher->dispatch($account, Dispatcher::INFO, $xmlBody);
    }

    public function testSupplementaryServices(): void
    {
        $account = new Account();
        $account->setProduction(false);
        $account->setLoginType(Account::LOGIN_NAME_PASSWORD);

        $xmlBody = 'XMLDATA';

        $clientInterface = $this->createClientMock();
        $requestFactory = $this->createRequestFactoryMock();
        $streamFactory = $this->createStreamFactoryMock();
        $uriFactory = $this->createUriFactoryMock();

        $streamInterface = $this->createMock(StreamInterface::class);
        $streamFactory->expects($this->once())
            ->method('createStream')
            ->with($xmlBody)
            ->willReturn($streamInterface);

        $requestMock = $this->createMock(RequestInterface::class);

        $map = [
            ['Connection', 'Keep-Alive', $requestMock],
            ['Accept-Encoding', 'gzip,deflate', $requestMock],
            ['Content-Type', 'text/xml; charset=utf-8', $requestMock],
            ['SOAPAction', '""', $requestMock],
            ['Authorization', sprintf('Basic %s', base64_encode($account->getLoginName() . ':' . $account->getPassword())), $requestMock],
        ];
        $requestMock
            ->expects($this->exactly(5))
            ->method('withHeader')
            ->will($this->returnValueMap($map));

        $requestMock
            ->expects($this->once())
            ->method('withBody')
            ->with($streamInterface)
            ->willReturn($requestMock);

        $uriMock = $this->createMock(UriInterface::class);
        $uriFactory->expects($this->once())
            ->method('createUri')
            ->with(...['uri' => 'https://ws1.czebox.cz/DS/DsManage'])
            ->willReturn($uriMock);

        $requestFactory
            ->expects($this->once())
            ->method('createRequest')
            ->with(...['method' => 'POST', 'uri' => $uriMock])
            ->willReturn($requestMock);

        $clientInterface->expects($this->once())
            ->method('sendRequest')
            ->with($requestMock);

        $dispatcher = new Dispatcher(
            $clientInterface,
            $requestFactory,
            $streamFactory,
            $uriFactory
        );
        $dispatcher->dispatch($account, Dispatcher::SUPPLEMENTARY, $xmlBody);
    }

    public function testAccessServices(): void
    {
        $account = new Account();
        $account->setProduction(false);
        $account->setLoginType(Account::LOGIN_NAME_PASSWORD);

        $xmlBody = 'XMLDATA';

        $clientInterface = $this->createClientMock();
        $requestFactory = $this->createRequestFactoryMock();
        $streamFactory = $this->createStreamFactoryMock();
        $uriFactory = $this->createUriFactoryMock();

        $streamInterface = $this->createMock(StreamInterface::class);
        $streamFactory->expects($this->once())
            ->method('createStream')
            ->with($xmlBody)
            ->willReturn($streamInterface);

        $requestMock = $this->createMock(RequestInterface::class);

        $map = [
            ['Connection', 'Keep-Alive', $requestMock],
            ['Accept-Encoding', 'gzip,deflate', $requestMock],
            ['Content-Type', 'text/xml; charset=utf-8', $requestMock],
            ['SOAPAction', '""', $requestMock],
            ['Authorization', sprintf('Basic %s', base64_encode($account->getLoginName() . ':' . $account->getPassword())), $requestMock],
        ];
        $requestMock
            ->expects($this->exactly(5))
            ->method('withHeader')
            ->will($this->returnValueMap($map));

        $requestMock
            ->expects($this->once())
            ->method('withBody')
            ->with($streamInterface)
            ->willReturn($requestMock);

        $uriMock = $this->createMock(UriInterface::class);
        $uriFactory->expects($this->once())
            ->method('createUri')
            ->with(...['uri' => 'https://ws1.czebox.cz/DS/DsManage'])
            ->willReturn($uriMock);

        $requestFactory
            ->expects($this->once())
            ->method('createRequest')
            ->with(...['method' => 'POST', 'uri' => $uriMock])
            ->willReturn($requestMock);

        $clientInterface->expects($this->once())
            ->method('sendRequest')
            ->with($requestMock);

        $dispatcher = new Dispatcher(
            $clientInterface,
            $requestFactory,
            $streamFactory,
            $uriFactory
        );
        $dispatcher->dispatch($account, Dispatcher::ACCESS, $xmlBody);
    }

    public function testSearchServices(): void
    {
        $account = new Account();
        $account->setProduction(false);
        $account->setLoginType(Account::LOGIN_NAME_PASSWORD);

        $xmlBody = 'XMLDATA';

        $clientInterface = $this->createClientMock();
        $requestFactory = $this->createRequestFactoryMock();
        $streamFactory = $this->createStreamFactoryMock();
        $uriFactory = $this->createUriFactoryMock();

        $streamInterface = $this->createMock(StreamInterface::class);
        $streamFactory->expects($this->once())
            ->method('createStream')
            ->with($xmlBody)
            ->willReturn($streamInterface);

        $requestMock = $this->createMock(RequestInterface::class);

        $map = [
            ['Connection', 'Keep-Alive', $requestMock],
            ['Accept-Encoding', 'gzip,deflate', $requestMock],
            ['Content-Type', 'text/xml; charset=utf-8', $requestMock],
            ['SOAPAction', '""', $requestMock],
            ['Authorization', sprintf('Basic %s', base64_encode($account->getLoginName() . ':' . $account->getPassword())), $requestMock],
        ];
        $requestMock
            ->expects($this->exactly(5))
            ->method('withHeader')
            ->will($this->returnValueMap($map));

        $requestMock
            ->expects($this->once())
            ->method('withBody')
            ->with($streamInterface)
            ->willReturn($requestMock);

        $uriMock = $this->createMock(UriInterface::class);
        $uriFactory->expects($this->once())
            ->method('createUri')
            ->with(...['uri' => 'https://ws1.czebox.cz/DS/df'])
            ->willReturn($uriMock);

        $requestFactory
            ->expects($this->once())
            ->method('createRequest')
            ->with(...['method' => 'POST', 'uri' => $uriMock])
            ->willReturn($requestMock);

        $clientInterface->expects($this->once())
            ->method('sendRequest')
            ->with($requestMock);

        $dispatcher = new Dispatcher(
            $clientInterface,
            $requestFactory,
            $streamFactory,
            $uriFactory
        );
        $dispatcher->dispatch($account, Dispatcher::SEARCH, $xmlBody);
    }

    public function testLoginAndPassword(): void
    {
        $account = new Account();
        $account->setProduction(false);
        $account->setLoginType(Account::LOGIN_NAME_PASSWORD);

        $xmlBody = 'XMLDATA';

        $clientInterface = $this->createClientMock();
        $requestFactory = $this->createRequestFactoryMock();
        $streamFactory = $this->createStreamFactoryMock();
        $uriFactory = $this->createUriFactoryMock();

        $streamInterface = $this->createMock(StreamInterface::class);
        $streamFactory->expects($this->once())
            ->method('createStream')
            ->with($xmlBody)
            ->willReturn($streamInterface);

        $requestMock = $this->createMock(RequestInterface::class);

        $map = [
            ['Connection', 'Keep-Alive', $requestMock],
            ['Accept-Encoding', 'gzip,deflate', $requestMock],
            ['Content-Type', 'text/xml; charset=utf-8', $requestMock],
            ['SOAPAction', '""', $requestMock],
            ['Authorization', sprintf('Basic %s', base64_encode($account->getLoginName() . ':' . $account->getPassword())), $requestMock],
        ];
        $requestMock
            ->expects($this->exactly(5))
            ->method('withHeader')
            ->will($this->returnValueMap($map));

        $requestMock
            ->expects($this->once())
            ->method('withBody')
            ->with($streamInterface)
            ->willReturn($requestMock);

        $uriMock = $this->createMock(UriInterface::class);
        $uriFactory->expects($this->once())
            ->method('createUri')
            ->with(...['uri' => 'https://ws1.czebox.cz/DS/dz'])
            ->willReturn($uriMock);

        $requestFactory
            ->expects($this->once())
            ->method('createRequest')
            ->with(...['method' => 'POST', 'uri' => $uriMock])
            ->willReturn($requestMock);

        $clientInterface->expects($this->once())
            ->method('sendRequest')
            ->with($requestMock);

        $dispatcher = new Dispatcher(
            $clientInterface,
            $requestFactory,
            $streamFactory,
            $uriFactory
        );
        $dispatcher->dispatch($account, Dispatcher::OPERATIONS, $xmlBody);
    }

    public function testSpisCert(): void
    {
        $account = $this->createAccountWithCertificate();
        $account->setProduction(false);
        $account->setLoginType(Account::LOGIN_SPIS_CERT);

        $xmlBody = 'XMLDATA';

        $clientInterface = $this->createClientMock();
        $requestFactory = $this->createRequestFactoryMock();
        $streamFactory = $this->createStreamFactoryMock();
        $uriFactory = $this->createUriFactoryMock();

        $streamInterface = $this->createMock(StreamInterface::class);
        $streamFactory->expects($this->once())
            ->method('createStream')
            ->with($xmlBody)
            ->willReturn($streamInterface);

        $requestMock = $this->createMock(RequestInterface::class);

        $map = [
            ['Connection', 'Keep-Alive', $requestMock],
            ['Accept-Encoding', 'gzip,deflate', $requestMock],
            ['Content-Type', 'text/xml; charset=utf-8', $requestMock],
            ['SOAPAction', '""', $requestMock],
        ];
        $requestMock
            ->expects($this->exactly(4))
            ->method('withHeader')
            ->will($this->returnValueMap($map));

        $requestMock
            ->expects($this->once())
            ->method('withBody')
            ->with($streamInterface)
            ->willReturn($requestMock);

        $uriMock = $this->createMock(UriInterface::class);
        $uriFactory->expects($this->once())
            ->method('createUri')
            ->with(...['uri' => 'https://ws1c.czebox.cz/cert/DS/dz'])
            ->willReturn($uriMock);

        $requestFactory
            ->expects($this->once())
            ->method('createRequest')
            ->with(...['method' => 'POST', 'uri' => $uriMock])
            ->willReturn($requestMock);

        $clientInterface->expects($this->once())
            ->method('sendRequest')
            ->with($requestMock);

        $dispatcher = new Dispatcher(
            $clientInterface,
            $requestFactory,
            $streamFactory,
            $uriFactory
        );
        $dispatcher->dispatch($account, Dispatcher::OPERATIONS, $xmlBody);
    }

    public function testLoginAndPasswordWithCertificate(): void
    {
        $account = $this->createAccountWithCertificate();
        $account->setLoginType(Account::LOGIN_CERT_LOGIN_NAME_PASSWORD);
        $account->setProduction(false);

        $xmlBody = 'XMLDATA';

        $clientInterface = $this->createClientMock();
        $requestFactory = $this->createRequestFactoryMock();
        $streamFactory = $this->createStreamFactoryMock();
        $uriFactory = $this->createUriFactoryMock();

        $streamInterface = $this->createMock(StreamInterface::class);
        $streamFactory->expects($this->once())
            ->method('createStream')
            ->with($xmlBody)
            ->willReturn($streamInterface);

        $requestMock = $this->createMock(RequestInterface::class);

        $map = [
            ['Connection', 'Keep-Alive', $requestMock],
            ['Accept-Encoding', 'gzip,deflate', $requestMock],
            ['Content-Type', 'text/xml; charset=utf-8', $requestMock],
            ['SOAPAction', '""', $requestMock],
            ['Authorization', sprintf('Basic %s', base64_encode($account->getLoginName() . ':' . $account->getPassword())), $requestMock],
        ];
        $requestMock
            ->expects($this->exactly(5))
            ->method('withHeader')
            ->will($this->returnValueMap($map));

        $requestMock
            ->expects($this->once())
            ->method('withBody')
            ->with($streamInterface)
            ->willReturn($requestMock);

        $uriMock = $this->createMock(UriInterface::class);
        $uriFactory->expects($this->once())
            ->method('createUri')
            ->with(...['uri' => 'https://ws1c.czebox.cz/certds/DS/dz'])
            ->willReturn($uriMock);

        $requestFactory
            ->expects($this->once())
            ->method('createRequest')
            ->with(...['method' => 'POST', 'uri' => $uriMock])
            ->willReturn($requestMock);

        $clientInterface->expects($this->once())
            ->method('sendRequest')
            ->with($requestMock);

        $dispatcher = new Dispatcher(
            $clientInterface,
            $requestFactory,
            $streamFactory,
            $uriFactory
        );
        $dispatcher->dispatch($account, Dispatcher::OPERATIONS, $xmlBody);
    }

    public function testHostedSpis(): void
    {
        $account = $this->createAccountWithCertificate();
        $account->setProduction(false);
        $account->setLoginType(Account::LOGIN_HOSTED_SPIS);
        $account->setDataBoxId('jajew');

        $xmlBody = 'XMLDATA';

        $clientInterface = $this->createClientMock();
        $requestFactory = $this->createRequestFactoryMock();
        $streamFactory = $this->createStreamFactoryMock();
        $uriFactory = $this->createUriFactoryMock();

        $streamInterface = $this->createMock(StreamInterface::class);
        $streamFactory->expects($this->once())
            ->method('createStream')
            ->with($xmlBody)
            ->willReturn($streamInterface);

        $requestMock = $this->createMock(RequestInterface::class);

        $map = [
            ['Connection', 'Keep-Alive', $requestMock],
            ['Accept-Encoding', 'gzip,deflate', $requestMock],
            ['Content-Type', 'text/xml; charset=utf-8', $requestMock],
            ['SOAPAction', '""', $requestMock],
            ['Authorization', sprintf('Basic %s', base64_encode($account->getDataBoxId())), $requestMock],
        ];
        $requestMock
            ->expects($this->exactly(5))
            ->method('withHeader')
            ->will($this->returnValueMap($map));

        $requestMock
            ->expects($this->once())
            ->method('withBody')
            ->with($streamInterface)
            ->willReturn($requestMock);

        $uriMock = $this->createMock(UriInterface::class);
        $uriFactory->expects($this->once())
            ->method('createUri')
            ->with(...['uri' => 'https://ws1c.czebox.cz/hspis/DS/dz'])
            ->willReturn($uriMock);

        $requestFactory
            ->expects($this->once())
            ->method('createRequest')
            ->with(...['method' => 'POST', 'uri' => $uriMock])
            ->willReturn($requestMock);

        $clientInterface->expects($this->once())
            ->method('sendRequest')
            ->with($requestMock);

        $dispatcher = new Dispatcher(
            $clientInterface,
            $requestFactory,
            $streamFactory,
            $uriFactory
        );
        $dispatcher->dispatch($account, Dispatcher::OPERATIONS, $xmlBody);
    }


    private function createAccountWithCertificate(): Account
    {
        $passPhrase = self::TEST_PASS_PHRASE;
        $pkcsContent = $this->generateP12Certificate($passPhrase);

        $cert_array = [];
        openssl_pkcs12_read($pkcsContent, $cert_array, $passPhrase);

        $account = new Account();
        $account->setPkcs12Certificate($pkcsContent, $passPhrase);
        return $account;
    }

    /**
     * @return MockObject|ClientInterface
     */
    private function createClientMock(): MockObject
    {
        return $this->createMock(ClientInterface::class);
    }

    /**
     * @return MockObject|RequestFactoryInterface
     */
    private function createRequestFactoryMock(): MockObject
    {
        return $this->createMock(RequestFactoryInterface::class);
    }

    /**
     * @return MockObject|StreamFactoryInterface
     */
    private function createStreamFactoryMock(): MockObject
    {
        return $this->createMock(StreamFactoryInterface::class);
    }

    /**
     * @return MockObject|UriFactoryInterface
     */
    private function createUriFactoryMock(): MockObject
    {
        return $this->createMock(UriFactoryInterface::class);
    }
}
