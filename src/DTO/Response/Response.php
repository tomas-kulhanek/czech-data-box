<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

abstract class Response
{
    abstract public function getStatus(): ResponseStatus;
}
