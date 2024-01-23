<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TomasKulhanek\CzechDataBox\DTO\Delivery;
use TomasKulhanek\CzechDataBox\Traits\DataMessageStatus;

#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(name: 'p:GetDeliveryInfoResponse', namespace: 'http://isds.czechpoint.cz/v20')]
class GetDeliveryInfo extends IResponse
{
    use DataMessageStatus;

    #[Serializer\Type(Delivery::class)]
    #[Serializer\SerializedName('p:dmDelivery')]
    #[Serializer\SkipWhenEmpty]
    #[Serializer\XmlElement(cdata: false)]
    #[Assert\Valid()]
    protected Delivery $delivery;

    public function getDelivery(): Delivery
    {
        return $this->delivery;
    }
}
