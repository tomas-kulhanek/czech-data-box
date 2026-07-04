<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\MessageAuthorItem;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'GetMessageAuthor2Response')]
class GetMessageAuthor2 extends IResponse
{
    use DataMessageStatus;

    /**
     * @var MessageAuthorItem[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\MessageAuthorItem>')]
    #[Serializer\XmlList(entry: 'maItem', inline: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dmMessageAuthor')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
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
