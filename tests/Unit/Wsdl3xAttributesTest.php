<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\DTO\Envelope;
use TomasKulhanek\CzechDataBox\DTO\MessageRecord;
use TomasKulhanek\CzechDataBox\DTO\PublishOwnId;
use TomasKulhanek\Serializer\SerializerFactory;

class Wsdl3xAttributesTest extends TestCase
{
    public function testMessageRecordDeserializesVodzAndSpecMessFlag(): void
    {
        $serializer = SerializerFactory::create();
        $xml = <<<XML_WRAP
<?xml version="1.0" encoding="UTF-8"?>
<p:dmRecord xmlns:p="https://isds.czechpoint.cz/v20" dmType="V" dmVODZ="true" specMessFlag="1">
  <p:dmID>1234567</p:dmID>
  <p:dmSenderID>abcdefg</p:dmSenderID>
  <p:dmSender>Testovací subjekt</p:dmSender>
  <p:dmSenderType>13</p:dmSenderType>
  <p:dmRecipient>Příjemce</p:dmRecipient>
  <p:dmMessageStatus>4</p:dmMessageStatus>
  <p:dmAttachmentSize>2</p:dmAttachmentSize>
</p:dmRecord>
XML_WRAP;
        $record = $serializer->deserialize($xml, MessageRecord::class, 'xml');
        self::assertTrue($record->isVodz());
        self::assertTrue($record->isSuspicious());
        self::assertSame(1, $record->getSpecMessFlag());
        self::assertSame(13, $record->getSenderType());
    }

    public function testMessageRecordWithoutNewAttributes(): void
    {
        $serializer = SerializerFactory::create();
        $xml = <<<XML_WRAP
<?xml version="1.0" encoding="UTF-8"?>
<p:dmRecord xmlns:p="https://isds.czechpoint.cz/v20" dmType="V">
  <p:dmID>1234567</p:dmID>
  <p:dmMessageStatus>4</p:dmMessageStatus>
</p:dmRecord>
XML_WRAP;
        $record = $serializer->deserialize($xml, MessageRecord::class, 'xml');
        self::assertFalse($record->isVodz());
        self::assertFalse($record->isSuspicious());
        self::assertNull($record->getSpecMessFlag());
    }

    public function testEnvelopeSerializesPublishOwnIdWithIdLevel(): void
    {
        $serializer = SerializerFactory::create();
        $envelope = new Envelope();
        $envelope->setAnnotation('Test');
        $envelope->setPublishOwnId(new PublishOwnId()->setValue(true)->setIdLevel(3));

        $xml = $serializer->serialize($envelope, 'xml');
        self::assertStringContainsString('IdLevel="3"', $xml);
        self::assertStringContainsString('dmPublishOwnID', $xml);
        self::assertStringContainsString('>true<', $xml);
    }

    public function testEnvelopeSetPublishOwnIdAcceptsBool(): void
    {
        $envelope = new Envelope();
        $envelope->setPublishOwnId(true);
        self::assertInstanceOf(PublishOwnId::class, $envelope->getPublishOwnId());
        self::assertTrue($envelope->getPublishOwnId()->getValue());
        self::assertNull($envelope->getPublishOwnId()->getIdLevel());
    }
}
