<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use TomasKulhanek\Tests\CzechDataBox\SerializerTrait;
use DOMDocument;
use DOMElement;
use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\DTO\Request\AddDataBoxUser2;
use TomasKulhanek\CzechDataBox\DTO\Request\DeleteDataBoxUser2;
use TomasKulhanek\CzechDataBox\DTO\Request\GetDataBoxUsers2;
use TomasKulhanek\CzechDataBox\DTO\Response\GetDataBoxUsers2 as GetDataBoxUsers2Response;
use TomasKulhanek\CzechDataBox\DTO\UserInfoExt2;

class DataBoxUserManagementTest extends TestCase
{
    use SerializerTrait;

    private const string NAMESPACE = 'https://isds.czechpoint.cz/v20';

    public function testGetDataBoxUsers2RequestSerialization(): void
    {
        $serializer = self::createSerializer();
        $request = new GetDataBoxUsers2();
        $request->setDataBoxId('abcdefg');

        $xml = $serializer->serialize($request, 'xml');
        self::assertStringContainsString('<GetDataBoxUsers2 ', $xml);
        self::assertStringContainsString('<dbID>abcdefg</dbID>', $xml);
        self::assertStringNotContainsString('dbApproved', $xml);
    }

    public function testDeleteDataBoxUser2RequestElementOrder(): void
    {
        $serializer = self::createSerializer();
        $request = new DeleteDataBoxUser2();
        $request->setDataBoxId('abcdefg');
        $request->setIsdsId('USR123');
        $request->setExternRefNumber('cj-42');

        $xml = $serializer->serialize($request, 'xml');
        self::assertMatchesRegularExpression(
            '~<dbID>abcdefg</dbID>\s*<isdsID>USR123</isdsID>\s*<dbExternRefNumber>cj-42</dbExternRefNumber>~',
            $xml
        );
    }

    public function testAddDataBoxUser2RequestElementOrder(): void
    {
        $serializer = self::createSerializer();
        $userInfo = new UserInfoExt2();
        $userInfo->setAifoTicket('ticket-1');
        $userInfo->setGivenNames('Jan');
        $userInfo->setLastName('Novák');
        $userInfo->setUserType('SECONDARY_USER');
        $userInfo->setUserPrivils(255);

        $request = new AddDataBoxUser2();
        $request->setDataBoxId('abcdefg');
        $request->setUserInfo($userInfo);
        $request->setVirtual(true);
        $request->setEmail('jan.novak@example.com');
        $request->setApproved(true);
        $request->setExternRefNumber('cj-42');

        $document = new DOMDocument();
        $document->loadXML($serializer->serialize($request, 'xml'));

        // XSD tAddDBUserInput2 je xs:sequence a schema ma elementFormDefault="qualified":
        // rozhoduje poradi a namespace prvku, prefix je detail serializace.
        self::assertSame(
            ['dbID', 'dbUserInfo', 'dbVirtual', 'email', 'dbApproved', 'dbExternRefNumber'],
            $this->childElementNames($document)
        );

        $userInfoElement = $document->getElementsByTagNameNS(self::NAMESPACE, 'dbUserInfo')->item(0);
        self::assertInstanceOf(DOMElement::class, $userInfoElement);
        self::assertSame('ticket-1', $userInfoElement->getAttribute('AIFOTicket'));

        self::assertSame('true', $this->childElementValue($document, 'dbVirtual'));
        self::assertSame('jan.novak@example.com', $this->childElementValue($document, 'email'));
        self::assertSame('true', $this->childElementValue($document, 'dbApproved'));
        self::assertSame('cj-42', $this->childElementValue($document, 'dbExternRefNumber'));
    }

    public function testAddDataBoxUser2RequestSkipsEmptyOptionalElements(): void
    {
        $serializer = self::createSerializer();
        $userInfo = new UserInfoExt2();
        $userInfo->setGivenNames('Jan');
        $userInfo->setLastName('Novák');

        $request = new AddDataBoxUser2();
        $request->setDataBoxId('abcdefg');
        $request->setUserInfo($userInfo);

        $document = new DOMDocument();
        $document->loadXML($serializer->serialize($request, 'xml'));

        self::assertSame(['dbID', 'dbUserInfo'], $this->childElementNames($document));
    }

    /**
     * @return list<string>
     */
    private function childElementNames(DOMDocument $document): array
    {
        self::assertInstanceOf(DOMElement::class, $document->documentElement);

        $names = [];
        foreach ($document->documentElement->childNodes as $node) {
            if (!$node instanceof DOMElement) {
                continue;
            }
            self::assertSame(self::NAMESPACE, $node->namespaceURI);
            self::assertNotNull($node->localName);
            $names[] = $node->localName;
        }

        return $names;
    }

    private function childElementValue(DOMDocument $document, string $localName): string
    {
        $element = $document->getElementsByTagNameNS(self::NAMESPACE, $localName)->item(0);
        self::assertInstanceOf(DOMElement::class, $element);

        return $element->textContent;
    }

    public function testGetDataBoxUsers2ResponseDeserialization(): void
    {
        $xml = <<<XML_WRAP
<?xml version="1.0" encoding="UTF-8"?>
<p:GetDataBoxUsers2Response xmlns:p="https://isds.czechpoint.cz/v20">
  <p:dbUsers>
    <p:dbUserInfo AIFOTicket="ticket-1">
      <p:aifoIsds>true</p:aifoIsds>
      <p:pnGivenNames>Jan</p:pnGivenNames>
      <p:pnLastName>Novák</p:pnLastName>
      <p:isdsID>USR123</p:isdsID>
      <p:userType>PRIMARY_USER</p:userType>
      <p:userPrivils>255</p:userPrivils>
    </p:dbUserInfo>
  </p:dbUsers>
  <p:dbStatus>
    <p:dbStatusCode>0000</p:dbStatusCode>
    <p:dbStatusMessage>Ok.</p:dbStatusMessage>
  </p:dbStatus>
</p:GetDataBoxUsers2Response>
XML_WRAP;
        $response = self::deserializeXml($xml, GetDataBoxUsers2Response::class);
        self::assertCount(1, $response->getUsers());
        $user = $response->getUsers()[0];
        self::assertSame('ticket-1', $user->getAifoTicket());
        self::assertSame('Jan', $user->getGivenNames());
        self::assertSame('USR123', $user->getIsdsId());
        self::assertSame('PRIMARY_USER', $user->getUserType());
        self::assertSame(255, $user->getUserPrivils());
        self::assertSame('0000', $response->getStatus()->getCode());
    }
}
