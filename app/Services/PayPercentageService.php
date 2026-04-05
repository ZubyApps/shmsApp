<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\Visit;

Class PayPercentageService
{
    public function __construct()
    {
    }

    public function individual_Family(Visit $visit): int|float|null
    {
        return $visit->total_hms_bill && $visit->total_hms_bill != $visit->discount  ? round((float)($visit->total_paid / ($visit->total_hms_bill - $visit->discount)) * 100, 2) : null;
    }

    public function hmo_Retainership(Visit $visit): int|float|null
    {
        return $visit->total_hms_bill && $visit->total_hms_bill != $visit->discount ? round((float)($visit->totalApprovedBills() / ($visit->total_hms_bill - $visit->discount)) * 100) : null;
    }

    public function nhis(Visit $visit): int|float|null
    {
        return $visit->total_hms_bill && $visit->total_nhis_bill != $visit->discount ? round((float)(($visit->total_paid) / ($visit->total_nhis_bill - $visit->discount)) * 100, 2) : null;
    }
}