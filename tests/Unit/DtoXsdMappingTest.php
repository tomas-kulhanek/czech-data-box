<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use TomasKulhanek\Tests\CzechDataBox\SerializerTrait;
use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\DTO\DataBoxResult;
use TomasKulhanek\CzechDataBox\DTO\Delivery;
use TomasKulhanek\CzechDataBox\DTO\Request\DTInfo;

class DtoXsdMappingTest extends TestCase
{
    use SerializerTrait;

    public function testDeliveryEventTimeIsDeserialized(): void
    {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<p:dmDelivery xmlns:p="https://isds.czechpoint.cz/v20">
  <p:dmDm>
    <p:dmID>1234567</p:dmID>
  </p:dmDm>
  <p:dmHash algorithm="SHA-256">abcdef</p:dmHash>
  <p:dmQTimestamp>dGVzdA==</p:dmQTimestamp>
  <p:dmEvents>
    <p:dmEvent>
      <p:dmEventTime>2026-06-26T08:30:00.000+02:00</p:dmEventTime>
      <p:dmEventDescr>EV0: Podáno</p:dmEventDescr>
    </p:dmEvent>
  </p:dmEvents>
</p:dmDelivery>
XML;
        $delivery = self::deserializeXml($xml, Delivery::class);
        $events = $delivery->getEvents();
        self::assertCount(1, $events);
        self::assertNotNull($events[0]->getTime());
        self::assertSame('2026-06-26T08:30:00+02:00', $events[0]->getTime()->format('c'));
        self::assertSame('EV0: Podáno', $events[0]->getDescription());
    }

    public function testDtInfoRequestUsesLowercaseDbId(): void
    {
        $serializer = self::createSerializer();
        $request = new DTInfo();
        $request->setDataBoxId('abcdefg');
        $xml = $serializer->serialize($request, 'xml');
        self::assertStringContainsString('<dbId>abcdefg</dbId>', $xml);
        self::assertStringNotContainsString('<dbID>', $xml);
    }

    public function testDataBoxResultMapsDbIdOvm(): void
    {
        $xml = <<<XML_WRAP
<?xml version="1.0" encoding="UTF-8"?>
<p:dbResult xmlns:p="https://isds.czechpoint.cz/v20">
  <p:dbID>abcdefg</p:dbID>
  <p:dbType>OVM</p:dbType>
  <p:dbName>Testovací úřad</p:dbName>
  <p:dbAddress>Testovací 1, Praha</p:dbAddress>
  <p:dbBiDate xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
  <p:dbICO>12345678</p:dbICO>
  <p:dbIdOVM>12345678</p:dbIdOVM>
  <p:dbSendOptions>ALL</p:dbSendOptions>
</p:dbResult>
XML_WRAP;
        $result = self::deserializeXml($xml, DataBoxResult::class);
        self::assertSame('12345678', $result->getDataBoxIdOvm());
        self::assertSame('OVM', $result->getDataBoxType());
        self::assertSame('ALL', $result->getDataBoxSendOptions());
    }
}
