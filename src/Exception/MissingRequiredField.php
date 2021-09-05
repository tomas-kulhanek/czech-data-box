<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Exception;

use Exception;

class MissingRequiredField extends Exception
{
    public function __construct(string $fieldName)
    {
        parent::__construct(sprintf('The required field \'%s\' is empty.', $fieldName));
    }
}
