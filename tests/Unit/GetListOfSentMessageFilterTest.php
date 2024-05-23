<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use PHPUnit\Framework\TestCase;
use TomasKulhanek\CzechDataBox\DTO\Request\GetListOfSentMessages;
use TomasKulhanek\CzechDataBox\Enum\FilterEnum;
use TomasKulhanek\Serializer\SerializerFactory;

class GetListOfSentMessageFilterTest extends TestCase
{
    public function testAllFilterCombinations(): void
    {
        $filterEnumValues = FilterEnum::cases();
        $totalValues = count($filterEnumValues);
        $totalCombinations = 2 ** $totalValues - 1;

        for ($i = 1; $i <= $totalCombinations; $i++) {
            $combinations = [];

            $expectation = [];
            for ($j = 0; $j < $totalValues; $j++) {
                if ($i & (1 << $j)) {
                    $combinations[] = $filterEnumValues[$j];
                    $expectation[] = $filterEnumValues[$j];
                }
            }
            foreach ($combinations as $combination) {
                if ($combination === FilterEnum::ALL) {
                    $expectation = [$combination];
                    break;
                }
            }

            $a = new GetListOfSentMessages();
            $a->setStatusFilter(...$combinations);
            $this->assertSame($expectation, $a->getStatusFilter());
        }
    }

    public function testFilerAll(): void
    {
        $a = new GetListOfSentMessages();
        $a->setStatusFilter(FilterEnum::ALL, FilterEnum::DELETED);
        $this->assertSame([FilterEnum::ALL], $a->getStatusFilter());
    }

    public function testXmlFilterAll(): void
    {
        $serializer = SerializerFactory::create();
        $a = new GetListOfSentMessages();
        $a->setStatusFilter(FilterEnum::ALL, FilterEnum::DELETED);
        $expectingXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<GetListOfSentMessages xmlns="https://isds.czechpoint.cz/v20" xmlns:p="https://isds.czechpoint.cz/v20">
  <dmStatusFilter>-1</dmStatusFilter>
</GetListOfSentMessages>

XML;
        $this->assertSame($expectingXml, $serializer->serialize($a, 'xml'));
    }

    public function testXmlFilterSpecific(): void
    {
        $serializer = SerializerFactory::create();
        $a = new GetListOfSentMessages();
        $a->setStatusFilter(FilterEnum::DELETED);
        $expectingXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<GetListOfSentMessages xmlns="https://isds.czechpoint.cz/v20" xmlns:p="https://isds.czechpoint.cz/v20">
  <dmStatusFilter>512</dmStatusFilter>
</GetListOfSentMessages>

XML;
        $this->assertSame($expectingXml, $serializer->serialize($a, 'xml'));
    }

    public function testXmlFilterNotSpecified(): void
    {
        $serializer = SerializerFactory::create();
        $a = new GetListOfSentMessages();
        $expectingXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<GetListOfSentMessages xmlns="https://isds.czechpoint.cz/v20" xmlns:p="https://isds.czechpoint.cz/v20">
  <dmStatusFilter>-1</dmStatusFilter>
</GetListOfSentMessages>

XML;
        $this->assertSame($expectingXml, $serializer->serialize($a, 'xml'));
    }
}
