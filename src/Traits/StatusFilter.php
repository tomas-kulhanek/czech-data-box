<?php

namespace TomasKulhanek\CzechDataBox\Traits;

use TomasKulhanek\CzechDataBox\Enum\FilterEnum;

trait StatusFilter
{
    /**
     * @return non-empty-list<FilterEnum>
     */
    public function decodeFilters(int $filters): array
    {
        if ($filters === -1) {
            return [FilterEnum::ALL];
        }
        $selectedFilters = [];

        foreach (FilterEnum::cases() as $enumValue) {
            if ($enumValue === FilterEnum::ALL) {
                continue;
            }
            if ($filters & (1 << $enumValue->value)) {
                $selectedFilters[] = $enumValue;
            }
        }
        if ($selectedFilters === []) {
            return [FilterEnum::ALL];
        }

        return $selectedFilters;
    }

    public function encodeFilters(FilterEnum ...$statuses): int
    {
        $statusFilter = 0;
        foreach ($statuses as $status) {
            if ($status === FilterEnum::ALL) {
                return -1;
            }
            $statusFilter += 2 ** $status->value;
        }
        if ($statusFilter === 0) {
            return -1;
        }
        return $statusFilter;
    }
}
