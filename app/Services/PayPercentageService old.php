<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\Visit;

Class PayPercentageService
{
    public function __construct()
    {
    }

    // public function individual_Family(Visit $visit): int|float|null
    // {
    //     return $visit->total_hms_bill && $visit->total_hms_bill != $visit->discount  ? round((float)($visit->total_paid / ($visit->total_hms_bill - $visit->discount)) * 100, 2) : null;
    // }

    // public function hmo_Retainership(Visit $visit): int|float|null
    // {
    //     return $visit->total_hms_bill && $visit->total_hms_bill != $visit->discount ? round((float)($visit->totalApprovedBills() / ($visit->total_hms_bill - $visit->discount)) * 100) : null;
    // }

    // public function nhis(Visit $visit): int|float|null
    // {
    //     return $visit->total_hms_bill && $visit->total_nhis_bill != $visit->discount ? round((float)(($visit->total_paid) / ($visit->total_nhis_bill - $visit->discount)) * 100, 2) : null;
    // }

    public function individual_Family(Visit $visit): int|float|null
    {
        return $this->calculatePercentage(
            (float) $visit->total_hms_bill,
            (float) $visit->total_paid,
            (float) $visit->total_hms_bill,
            (float) $visit->discount
        );
    }

    public function hmo_Retainership(Visit $visit): int|float|null
    {
        return $this->calculatePercentage(
            (float) $visit->total_hms_bill,
            (float) $visit->totalApprovedBills(),
            (float) $visit->total_hms_bill,
            (float) $visit->discount
        );
    }

    public function nhis(Visit $visit): int|float|null
    {
        return $this->calculatePercentage(
            (float) $visit->total_hms_bill,
            (float) $visit->total_paid,
            (float) $visit->total_nhis_bill,
            (float) $visit->discount
        );
    }

    /**
     * Centralized percentage calculation with an explicit division-by-zero guard.
     */
    private function calculatePercentage(float $billCheck, float $numerator, float $bill, float $discount): int|float|null
    {
        $denominator = $bill - $discount;

        if ($billCheck > 0 && $denominator != 0) {
            return round(($numerator / $denominator) * 100, 2);
        }

        return null;
    }
}