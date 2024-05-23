<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'ISDSSearch3')]
class ISDSSearch3 implements IRequest
{
    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('searchText')]
    private string $searchText;

    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('searchType')]
    private string $searchType;

    #[Serializer\Type('string')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('searchScope')]
    private string $searchScope;

    #[Serializer\Type('int')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('page')]
    private int $page = 0;

    #[Serializer\Type('int')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('pageSize')]
    private int $pageSize = 20;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('highlighting')]
    private ?bool $highlighting = null;

    public function getSearchText(): string
    {
        return $this->searchText;
    }

    public function setSearchText(string $searchText): ISDSSearch3
    {
        $this->searchText = $searchText;
        return $this;
    }

    public function getSearchType(): string
    {
        return $this->searchType;
    }

    public function setSearchType(string $searchType): ISDSSearch3
    {
        $this->searchType = $searchType;
        return $this;
    }

    public function getSearchScope(): string
    {
        return $this->searchScope;
    }

    public function setSearchScope(string $searchScope): ISDSSearch3
    {
        $this->searchScope = $searchScope;
        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): ISDSSearch3
    {
        $this->page = $page;
        return $this;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function setPageSize(int $pageSize): ISDSSearch3
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    public function isHighlighting(): ?bool
    {
        return $this->highlighting;
    }

    public function setHighlighting(bool $highlighting): ISDSSearch3
    {
        $this->highlighting = $highlighting;
        return $this;
    }
}
