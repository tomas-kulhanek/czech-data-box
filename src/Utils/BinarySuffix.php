<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Utils;

use InvalidArgumentException;

class BinarySuffix
{
    public const CONVERT_THRESHOLD = 1024;

    private int $number;

    /**
     * @var array<int,string>
     */
    private array $binaryPrefixes = [
        1125899906842624 => '%d PB',
        1099511627776 => '%d TB',
        1073741824 => '%d GB',
        1048576 => '%d MB',
        1024 => '%d kB',
        0 => '%d bytes',
    ];

    /**
     * @param int $number
     * @throws InvalidArgumentException
     */
    public function __construct(int $number)
    {
        if (!is_numeric($number)) {
            throw new InvalidArgumentException('Binary suffix converter accept only numeric values.');
        }
        $this->number = $number;
        /*
         * Workaround for 32-bit systems which ignore array ordering when
         * dropping values over 2^32-1
         */
        krsort($this->binaryPrefixes);
    }

    /**
     * @param int $number
     * @return int|string
     */
    public static function convert(int $number)
    {
        $obj = new self($number);
        return $obj->doConvert();
    }

    /**
     * @return int|string
     */
    public function doConvert()
    {
        if ($this->number < 0) {
            return $this->number;
        }
        foreach ($this->binaryPrefixes as $size => $unitPattern) {
            if ($size <= $this->number) {
                $value = ($this->number >= self::CONVERT_THRESHOLD) ? $this->number / (float) $size : $this->number;

                return sprintf($unitPattern, $value);
            }
        }
        return $this->number;
    }
}
