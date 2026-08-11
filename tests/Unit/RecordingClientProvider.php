<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;
use TomasKulhanek\CzechDataBox\Provider\ClientProviderInterface;

final class RecordingClientProvider implements ClientProviderInterface
{
    public ?string $capturedBody = null;

    public ?ServiceTypeEnum $capturedServiceType = null;

    public function __construct(private readonly string $response)
    {
    }

    public function sendRequest(Account $account, ServiceTypeEnum $serviceType, string $xmlBody): string
    {
        $this->capturedBody = $xmlBody;
        $this->capturedServiceType = $serviceType;

        return $this->response;
    }
}
