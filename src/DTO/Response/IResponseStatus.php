<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

interface IResponseStatus
{
    public function getCode(): string;

    public function setCode(string $code): self;

    public function getMessage(): string;

    public function setMessage(string $message): self;

    public function getRefNumber(): ?string;

    public function setRefNumber(?string $refNumber): self;

    public function isOk(): bool;
}
