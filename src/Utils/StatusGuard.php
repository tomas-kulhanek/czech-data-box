<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Utils;

use TomasKulhanek\CzechDataBox\DTO\Response\CreateMessage;
use TomasKulhanek\CzechDataBox\DTO\Response\Response;
use TomasKulhanek\CzechDataBox\DTO\Response\ResponseStatus;
use TomasKulhanek\CzechDataBox\Exception\IsdsStatusError;

final class StatusGuard
{
    private const array KNOWN_CODE_HINTS = [
        '1281' => 'VoDZ zprávu nelze stáhnout přes ws1 — použijte VoDZ operace na ws2',
        '1201' => 'schránka je znepřístupněna',
        '2046' => 'antivirová kontrola nedoběhla — timeout',
    ];

    private function __construct()
    {
    }

    /**
     * @throws IsdsStatusError
     */
    public static function assertStatusOk(ResponseStatus $status): void
    {
        if ($status->isOk()) {
            return;
        }
        throw new IsdsStatusError(
            $status->getCode(),
            $status->getMessage(),
            $status->getRefNumber(),
            self::KNOWN_CODE_HINTS[$status->getCode()] ?? null,
        );
    }

    /**
     * @throws IsdsStatusError
     */
    public static function assertOk(Response $response): void
    {
        self::assertStatusOk($response->getStatus());
        if ($response instanceof CreateMessage) {
            foreach ($response->getMultipleStatus() as $messageStatus) {
                self::assertStatusOk($messageStatus->getStatus());
            }
        }
    }
}
