<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\DataBoxResult;
use TomasKulhanek\CzechDataBox\Traits\DataBoxStatus;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'p:ISDSSearch3Response', namespace: 'http://isds.czechpoint.cz/v20')]
class ISDSSearch3 extends IResponse
{
    use DataBoxStatus;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\SerializedName('totalCount')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?int $totalCount = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\SerializedName('currentCount')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?int $currentCount = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('int')]
    #[Serializer\SerializedName('position')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?int $position = null;

    #[Serializer\SkipWhenEmpty]
    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('lastPage')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    protected ?bool $lastPage = null;

    /**
     * @var DataBoxResult[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\DataBoxResult>')]
    #[Serializer\XmlList(entry: 'dbResult', inline: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Serializer\SerializedName('dbResults')]
    #[Serializer\XmlElement(cdata: false, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Assert\All([
        new Assert\Type(type: DataBoxResult::class)
    ])]
    #[Assert\Valid()]
    protected array $result = [];

    public function getTotalCount(): ?int
    {
        return $this->totalCount;
    }

    public function setTotalCount(?int $totalCount): ISDSSearch3
    {
        $this->totalCount = $totalCount;
        return $this;
    }

    public function getCurrentCount(): ?int
    {
        return $this->currentCount;
    }

    public function setCurrentCount(?int $currentCount): ISDSSearch3
    {
        $this->currentCount = $currentCount;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): ISDSSearch3
    {
        $this->position = $position;
        return $this;
    }

    public function getLastPage(): ?bool
    {
        return $this->lastPage;
    }

    public function setLastPage(?bool $lastPage): ISDSSearch3
    {
        $this->lastPage = $lastPage;
        return $this;
    }

    /**
     * @return DataBoxResult[]
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * @param DataBoxResult[] $result
     * @return ISDSSearch3
     */
    public function setResult(array $result): ISDSSearch3
    {
        $this->result = $result;
        return $this;
    }
}
