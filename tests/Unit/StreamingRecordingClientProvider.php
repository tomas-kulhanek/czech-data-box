<?php

declare(strict_types=1);

namespace TomasKulhanek\Tests\CzechDataBox\Unit;

use TomasKulhanek\CzechDataBox\Account;
use TomasKulhanek\CzechDataBox\Enum\ServiceTypeEnum;
use TomasKulhanek\CzechDataBox\Provider\StreamedRequestBody;
use TomasKulhanek\CzechDataBox\Provider\StreamingClientProviderInterface;

final class StreamingRecordingClientProvider implements StreamingClientProviderInterface
{
    public ?string $bufferedBody = null;

    public ?StreamedRequestBody $streamedBody = null;

    public function __construct(private readonly string $response)
    {
    }

    public function sendRequest(
        Account $account,
        ServiceTypeEnum $serviceType,
        string $xmlBody,
        ?int $maxResponseSize = null
    ): string {
        $this->bufferedBody = $xmlBody;

        return $this->response;
    }

    public function sendStreamedRequest(
        Account $account,
        ServiceTypeEnum $serviceType,
        StreamedRequestBody $body,
        ?int $maxResponseSize = null
    ): string {
        $this->streamedBody = $body;

        return $this->response;
    }
}
