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
        1_125_899_906_842_624 => '%d PB',
        1_099_511_627_776 => '%d TB',
        1_073_741_824 => '%d GB',
        1_048_576 => '%d MB',
        1024 => '%d kB',
        0 => '%d bytes',
    ];

    /**
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
