<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use TomasKulhanek\Tests\CzechDataBox\SerializerTrait;
use LogicException;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\DTO\Request\EraseMessage;
use TomasKulhanek\CzechDataBox\DTO\Request\GetListOfErasedMessages;
use TomasKulhanek\CzechDataBox\DTO\Request\SuspMessageReport;
use TomasKulhanek\CzechDataBox\DTO\Response\GetConstants;
use TomasKulhanek\CzechDataBox\DTO\Response\GetDataBoxAddress;
use TomasKulhanek\CzechDataBox\DTO\Response\GetListForNotifications;
use TomasKulhanek\CzechDataBox\DTO\Response\GetMessageAuthor2;
use TomasKulhanek\CzechDataBox\DTO\Response\GetUserInfoFromLogin2;

class NewOperationsTest extends TestCase
{
    use SerializerTrait;

    public function testEraseMessageRequestIsSerialized(): void
    {
        $serializer = self::createSerializer();
        $request = new EraseMessage();
        $request->setDataMessageId('1234567');
        $request->setIncoming(true);
        $xml = $serializer->serialize($request, 'xml');
        self::assertStringContainsString('EraseMessage', $xml);
        self::assertStringContainsString('<dmID>1234567</dmID>', $xml);
        self::assertStringContainsString('<dmIncoming>true</dmIncoming>', $xml);
        self::assertLessThan(
            (int) strpos((string) $xml, '<dmIncoming>'),
            (int) strpos((string) $xml, '<dmID>'),
            'dmID must be serialized before dmIncoming'
        );
    }

    public function testSuspMessageReportRequestIsSerialized(): void
    {
        $serializer = self::createSerializer();
        $request = new SuspMessageReport();
        $request->setDataMessageId('7654321');
        $request->setReporterName('Jan Novák');
        $request->setReporterMail('jan@example.com');
        $request->setReporterPhone('+420123456789');
        $request->setAllowComplete(false);
        $request->setNote('Podezřelá zpráva');
        $xml = $serializer->serialize($request, 'xml');
        self::assertStringContainsString('SuspMessageReport', $xml);
        self::assertStringContainsString('<dmID>7654321</dmID>', $xml);
        self::assertStringContainsString('<repName>Jan Novák</repName>', $xml);
        self::assertStringContainsString('<repMail>jan@example.com</repMail>', $xml);
        self::assertStringContainsString('<repTel>+420123456789</repTel>', $xml);
        self::assertStringContainsString('<allowComplete>false</allowComplete>', $xml);
        self::assertStringContainsString('<note>Podezřelá zpráva</note>', $xml);
        $order = ['<dmID>', '<repName>', '<repMail>', '<repTel>', '<allowComplete>', '<note>'];
        $previous = -1;
        foreach ($order as $element) {
            $position = (int) strpos((string) $xml, $element);
            self::assertGreaterThan($previous, $position, sprintf('Element %s is out of order', $element));
            $previous = $position;
        }
    }

    public function testGetListOfErasedMessagesRequestIsSerialized(): void
    {
        $serializer = self::createSerializer();
        $request = new GetListOfErasedMessages();
        $request->setFromDate(new DateTimeImmutable('2026-01-01'));
        $request->setToDate(new DateTimeImmutable('2026-01-31'));
        $request->setMessageType(GetListOfErasedMessages::MESSAGE_TYPE_SENT);
        $request->setOutFormat(GetListOfErasedMessages::OUT_FORMAT_XML);
        $xml = $serializer->serialize($request, 'xml');
        self::assertStringContainsString('GetListOfErasedMessages', $xml);
        self::assertStringContainsString('<dmFromDate>2026-01-01</dmFromDate>', $xml);
        self::assertStringContainsString('<dmToDate>2026-01-31</dmToDate>', $xml);
        self::assertStringContainsString('<dmMessageType>SENT</dmMessageType>', $xml);
        self::assertStringContainsString('<dmOutFormat>XML</dmOutFormat>', $xml);
        self::assertStringNotContainsString('<dmYear>', $xml);
        self::assertStringNotContainsString('<dmMonth>', $xml);
        $order = ['<dmFromDate>', '<dmToDate>', '<dmMessageType>', '<dmOutFormat>'];
        $previous = -1;
        foreach ($order as $element) {
            $position = (int) strpos((string) $xml, $element);
            self::assertGreaterThan($previous, $position, sprintf('Element %s is out of order', $element));
            $previous = $position;
        }
    }

    public function testGetMessageAuthor2ResponseIsDeserialized(): void
    {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<p:GetMessageAuthor2Response xmlns:p="https://isds.czechpoint.cz/v20">
  <p:dmMessageAuthor>
    <p:maItem key="userType" value="PRIMARY_USER"/>
    <p:maItem key="pnGivenNames" value="Jan"/>
    <p:maItem key="pnLastName" value="Novák"/>
  </p:dmMessageAuthor>
  <p:dmStatus>
    <p:dmStatusCode>0000</p:dmStatusCode>
    <p:dmStatusMessage>Operation successfully</p:dmStatusMessage>
  </p:dmStatus>
</p:GetMessageAuthor2Response>
XML;
        $response = self::deserializeXml($xml, GetMessageAuthor2::class);
        self::assertCount(3, $response->getAuthorItems());
        self::assertSame('userType', $response->getAuthorItems()[0]->getKey());
        self::assertSame('PRIMARY_USER', $response->getAuthorItems()[0]->getValue());
        $info = $response->getAuthorInfo();
        self::assertSame('Jan', $info['pnGivenNames']);
        self::assertSame('Novák', $info['pnLastName']);
        self::assertSame('0000', $response->getStatus()->getCode());
        self::assertTrue($response->getStatus()->isOk());
    }

    public function testGetListForNotificationsResponseIsDeserialized(): void
    {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<p:GetListForNotificationsResponse xmlns:p="https://isds.czechpoint.cz/v20">
  <p:ntfRecords>
    <p:ntfRecord>
      <p:ntfType>1</p:ntfType>
      <p:dmID>1234567</p:dmID>
      <p:dmPersonalDelivery>0</p:dmPersonalDelivery>
      <p:dmDeliveryTime>2026-06-26T08:30:00.000+02:00</p:dmDeliveryTime>
      <p:dbIDRecipient>abcdefg</p:dbIDRecipient>
      <p:dmAnnotation>Testovací zpráva</p:dmAnnotation>
      <p:dbIDSender>hijklmn</p:dbIDSender>
      <p:dmSender>Testovací úřad</p:dmSender>
    </p:ntfRecord>
  </p:ntfRecords>
  <p:ntfListContinues>false</p:ntfListContinues>
  <p:dmStatus>
    <p:dmStatusCode>0000</p:dmStatusCode>
    <p:dmStatusMessage>Operation successfully</p:dmStatusMessage>
  </p:dmStatus>
</p:GetListForNotificationsResponse>
XML;
        $response = self::deserializeXml($xml, GetListForNotifications::class);
        $records = $response->getRecords();
        self::assertCount(1, $records);
        self::assertSame(1, $records[0]->getNotificationType());
        self::assertSame('1234567', $records[0]->getDataMessageId());
        self::assertSame(0, $records[0]->getPersonalDelivery());
        self::assertNotNull($records[0]->getDeliveryTime());
        self::assertSame('2026-06-26T08:30:00+02:00', $records[0]->getDeliveryTime()->format('c'));
        self::assertSame('abcdefg', $records[0]->getRecipientId());
        self::assertSame('Testovací zpráva', $records[0]->getAnnotation());
        self::assertSame('hijklmn', $records[0]->getSenderId());
        self::assertSame('Testovací úřad', $records[0]->getSender());
        self::assertFalse($response->getListContinues());
        self::assertTrue($response->getStatus()->isOk());
    }

    public function testGetConstantsResponseIsDeserialized(): void
    {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<p:GetConstantsResponse xmlns:p="https://isds.czechpoint.cz/v20">
  <p:constRecords>
    <p:constRecord>
      <p:cName>MAX_ATTACHMENT_SIZE</p:cName>
      <p:cValue>20</p:cValue>
      <p:cFrom>2020-01-01</p:cFrom>
      <p:cTo>2030-12-31</p:cTo>
    </p:constRecord>
    <p:constRecord>
      <p:cName>MAX_RECIPIENTS</p:cName>
      <p:cValue>50</p:cValue>
      <p:cFrom>2020-01-01</p:cFrom>
    </p:constRecord>
  </p:constRecords>
  <p:dbStatus>
    <p:dbStatusCode>0000</p:dbStatusCode>
    <p:dbStatusMessage>Ok.</p:dbStatusMessage>
  </p:dbStatus>
</p:GetConstantsResponse>
XML;
        $response = self::deserializeXml($xml, GetConstants::class);
        $records = $response->getRecords();
        self::assertCount(2, $records);
        self::assertSame('MAX_ATTACHMENT_SIZE', $records[0]->getName());
        self::assertSame('20', $records[0]->getValue());
        self::assertNotNull($records[0]->getFrom());
        self::assertSame('2020-01-01', $records[0]->getFrom()->format('Y-m-d'));
        self::assertNotNull($records[0]->getTo());
        self::assertSame('2030-12-31', $records[0]->getTo()->format('Y-m-d'));
        self::assertSame('MAX_RECIPIENTS', $records[1]->getName());
        self::assertNull($records[1]->getTo());
        self::assertTrue($response->getStatus()->isOk());
    }

    public function testGetDataBoxAddressResponseIsDeserialized(): void
    {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<p:GetDataBoxAddressResponse xmlns:p="https://isds.czechpoint.cz/v20">
  <p:adCode>12345678</p:adCode>
  <p:adCity>Praha</p:adCity>
  <p:adDistrict>Praha 1</p:adDistrict>
  <p:adStreet>Testovací</p:adStreet>
  <p:adNumberInStreet>1</p:adNumberInStreet>
  <p:adNumberInMunicipality>100</p:adNumberInMunicipality>
  <p:adZipCode>11000</p:adZipCode>
  <p:adState>CZ</p:adState>
  <p:adRegistrationNumber>123</p:adRegistrationNumber>
  <p:adFullAddress1>Testovací 1/100</p:adFullAddress1>
  <p:adFullAddress2>110 00 Praha 1</p:adFullAddress2>
</p:GetDataBoxAddressResponse>
XML;
        $response = self::deserializeXml($xml, GetDataBoxAddress::class);
        self::assertSame('12345678', $response->getAdCode());
        self::assertSame('Praha', $response->getAdCity());
        self::assertSame('Praha 1', $response->getAdDistrict());
        self::assertSame('Testovací', $response->getAdStreet());
        self::assertSame('1', $response->getAdNumberInStreet());
        self::assertSame('100', $response->getAdNumberInMunicipality());
        self::assertSame('11000', $response->getAdZipCode());
        self::assertSame('CZ', $response->getAdState());
        self::assertSame('123', $response->getAdRegistrationNumber());
        self::assertSame('Testovací 1/100', $response->getAdFullAddress1());
        self::assertSame('110 00 Praha 1', $response->getAdFullAddress2());
        self::assertFalse($response->hasStatus());
        $this->expectException(LogicException::class);
        $response->getStatus();
    }

    public function testGetUserInfoFromLogin2ResponseIsDeserialized(): void
    {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<p:GetUserInfoFromLogin2Response xmlns:p="https://isds.czechpoint.cz/v20">
  <p:dbUserInfo>
    <p:aifoIsds>false</p:aifoIsds>
    <p:pnGivenNames>Jan</p:pnGivenNames>
    <p:pnLastName>Novák</p:pnLastName>
    <p:adCity>Praha</p:adCity>
    <p:adStreet>Testovací</p:adStreet>
    <p:adZipCode>11000</p:adZipCode>
    <p:adState>CZ</p:adState>
    <p:biDate>1980-05-15</p:biDate>
    <p:isdsID>ABC123</p:isdsID>
    <p:userType>PRIMARY_USER</p:userType>
    <p:userPrivils>255</p:userPrivils>
  </p:dbUserInfo>
  <p:dbStatus>
    <p:dbStatusCode>0000</p:dbStatusCode>
    <p:dbStatusMessage>Ok.</p:dbStatusMessage>
  </p:dbStatus>
</p:GetUserInfoFromLogin2Response>
XML;
        $response = self::deserializeXml($xml, GetUserInfoFromLogin2::class);
        $userInfo = $response->getUserInfo();
        self::assertNotNull($userInfo);
        self::assertFalse($userInfo->getAifoIsds());
        self::assertSame('Jan', $userInfo->getGivenNames());
        self::assertSame('Novák', $userInfo->getLastName());
        self::assertSame('Praha', $userInfo->getAdCity());
        self::assertSame('Testovací', $userInfo->getAdStreet());
        self::assertSame('11000', $userInfo->getAdZipCode());
        self::assertSame('CZ', $userInfo->getAdState());
        self::assertNotNull($userInfo->getBiDate());
        self::assertSame('1980-05-15', $userInfo->getBiDate()->format('Y-m-d'));
        self::assertSame('ABC123', $userInfo->getIsdsId());
        self::assertSame('PRIMARY_USER', $userInfo->getUserType());
        self::assertSame(255, $userInfo->getUserPrivils());
        self::assertTrue($response->getStatus()->isOk());
    }
}
