<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Exception;

use Exception;

final class IsdsStatusError extends Exception implements CzechDataBoxException
{
    public function __construct(
        public readonly string $statusCode,
        public readonly string $statusMessage,
        public readonly ?string $refNumber = null,
        ?string $hint = null,
    ) {
        $message = sprintf('ISDS returned status %s: %s', $this->statusCode, $this->statusMessage);
        if ($hint !== null) {
            $message .= sprintf(' (%s)', $hint);
        }
        parent::__construct($message);
    }
}
