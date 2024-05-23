<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\Delivery;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'GetDeliveryInfoResponse')]
class GetDeliveryInfo extends IResponse
{
    use DataMessageStatus;

    #[Serializer\Type(Delivery::class)]
    #[Serializer\SerializedName('dmDelivery')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\Valid()]
    protected Delivery $delivery;

    public function getDelivery(): Delivery
    {
        return $this->delivery;
    }
}
