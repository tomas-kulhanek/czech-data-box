<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox;

abstract class IResponse
{

    abstract public function getStatus(): IResponseStatus;

}
