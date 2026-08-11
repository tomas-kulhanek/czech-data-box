<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use TomasKulhanek\Tests\CzechDataBox\SerializerTrait;
use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\DTO\OwnerInfoExt2;
use TomasKulhanek\CzechDataBox\DTO\Request\FindDataBox2;
use TomasKulhanek\CzechDataBox\DTO\Response\FindDataBox2 as FindDataBox2Response;

class FindDataBox2Test extends TestCase
{
    use SerializerTrait;

    public function testRequestSerialization(): void
    {
        $serializer = self::createSerializer();
        $ownerInfo = new OwnerInfoExt2();
        $ownerInfo->setDataBoxType('OVM')
            ->setIc('12345678');
        $request = new FindDataBox2();
        $request->setOwnerInfo($ownerInfo);

        $xml = $serializer->serialize($request, 'xml');
        self::assertStringContainsString('<FindDataBox2 ', $xml);
        self::assertStringContainsString('dbOwnerInfo', $xml);
        self::assertStringContainsString('<p:dbType>OVM</p:dbType>', $xml);
        self::assertStringContainsString('<p:ic>12345678</p:ic>', $xml);
    }

    public function testResponseDeserialization(): void
    {
        $xml = <<<XML_WRAP
<?xml version="1.0" encoding="UTF-8"?>
<p:FindDataBox2Response xmlns:p="http://isds.czechpoint.cz/v20">
  <p:dbResults>
    <p:dbOwnerInfo>
      <p:dbID>abcdefg</p:dbID>
      <p:aifoIsds xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
      <p:dbType>OVM</p:dbType>
      <p:ic>12345678</p:ic>
      <p:pnGivenNames xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
      <p:pnLastName xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
      <p:firmName>Testovací úřad</p:firmName>
      <p:adCity>Praha</p:adCity>
      <p:dbIdOVM>12345678</p:dbIdOVM>
      <p:dbState>1</p:dbState>
      <p:dbOpenAddressing>false</p:dbOpenAddressing>
    </p:dbOwnerInfo>
  </p:dbResults>
  <p:dbStatus>
    <p:dbStatusCode>0000</p:dbStatusCode>
    <p:dbStatusMessage>Ok.</p:dbStatusMessage>
  </p:dbStatus>
</p:FindDataBox2Response>
XML_WRAP;
        $response = self::deserializeXml($xml, FindDataBox2Response::class);
        self::assertCount(1, $response->getResult());
        $owner = $response->getResult()[0];
        self::assertSame('abcdefg', $owner->getDataBoxId());
        self::assertSame('OVM', $owner->getDataBoxType());
        self::assertSame('12345678', $owner->getIc());
        self::assertSame('Testovací úřad', $owner->getFirmName());
        self::assertSame('12345678', $owner->getDataBoxIdOvm());
        self::assertSame(1, $owner->getDataBoxState());
        self::assertSame('0000', $response->getStatus()->getCode());
    }
}
