<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Response;

abstract class IResponse
{
    abstract public function getStatus(): IResponseStatus;
}
