<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Provider;

use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;

/**
 * Volitelné rozšíření {@see ClientProviderInterface} pro providery, které umí poslat tělo po částech.
 *
 * {@see \TomasKulhanek\CzechDataBox\Connector} ho použije automaticky; přílohy se pak base64 kódují
 * až při zápisu do spojení a paměť nezávisí na jejich velikosti. Provider, který rozhraní neimplementuje,
 * dostane celé tělo jako řetězec přes {@see ClientProviderInterface::sendRequest()}.
 */
interface StreamingClientProviderInterface extends ClientProviderInterface
{
    /**
     * @return string tělo odpovědi
     */
    public function sendStreamedRequest(
        Account $account,
        ServiceTypeEnum $serviceType,
        StreamedRequestBody $body,
        ?int $maxResponseSize = null
    ): string;
}
