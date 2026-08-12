<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\MessageAuthorItem;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'GetMessageAuthor2Response')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['authorItems', 'status'])]
class GetMessageAuthor2 extends DataMessageResponse
{
    /**
     * @var MessageAuthorItem[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\MessageAuthorItem>')]
    #[Serializer\XmlList(entry: 'maItem', inline: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmMessageAuthor')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Assert\All([
        new Assert\Type(type: MessageAuthorItem::class)
    ])]
    #[Assert\Valid()]
    protected array $authorItems = [];

    /**
     * @return MessageAuthorItem[]
     */
    public function getAuthorItems(): array
    {
        return $this->authorItems;
    }

    /**
     * @return array<string, string> Map of author attribute key => value.
     */
    public function getAuthorInfo(): array
    {
        $info = [];
        foreach ($this->authorItems as $item) {
            $info[$item->getKey()] = $item->getValue();
        }
        return $info;
    }
}
