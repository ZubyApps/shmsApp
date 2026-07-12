<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\Visit;

class PayPercentageService
{
    public function individual_Family(Visit $visit): int|float|null
    {
        return $this->calculatePercentage(
            billCheck: (float) $visit->total_hms_bill,
            numerator: (float) $visit->total_paid,
            discount:  (float) $visit->discount
        );
    }

    public function hmo_Retainership(Visit $visit): int|float|null
    {
        return $this->calculatePercentage(
            billCheck: (float) $visit->total_hms_bill,
            numerator: (float) $visit->totalApprovedBills(),
            discount:  (float) $visit->discount
        );
    }

    public function nhis(Visit $visit): int|float|null
    {
        return $this->calculatePercentage(
            billCheck: (float) $visit->total_hms_bill,
            numerator: (float) $visit->total_paid,
            discount:  (float) $visit->discount,
            bill:      (float) $visit->total_nhis_bill
        );
    }

    /**
     * Centralized percentage calculation with safeguards against division-by-zero 
     * and human input errors (like discounts exceeding the bill amount).
     */
    private function calculatePercentage(float $billCheck, float $numerator, float $discount, ?float $bill = null): int|float|null
    {
        $bill = $bill ?? $billCheck;
        $denominator = $bill - $discount;

        // Changing != 0 to > 0 catches division-by-zero AND negative denominators
        if ($billCheck > 0 && $denominator > 0) {
            return round(($numerator / $denominator) * 100, 2);
        }

        return null;
    }
}