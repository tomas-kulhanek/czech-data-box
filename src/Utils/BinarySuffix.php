<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Utils;

class BinarySuffix
{
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

    public function __construct(private readonly int $number)
    {
    }

    public static function convert(int $number): string
    {
        $obj = new self($number);
        return $obj->doConvert();
    }

    public function doConvert(): string
    {
        if ($this->number < 0) {
            return (string) $this->number;
        }
        foreach ($this->binaryPrefixes as $size => $unitPattern) {
            if ($size <= $this->number) {
                $value = ($this->number >= 1024) ? $this->number / (float) $size : $this->number;

                return sprintf($unitPattern, $value);
            }
        }
        return (string) $this->number;
    }
}
