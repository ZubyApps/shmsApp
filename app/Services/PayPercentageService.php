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
        return $visit->totalHmsBills() ? round((float)($visit->totalPayments() / ($visit->totalHmsBills() - $visit->discount)) * 100) : null;
    }

    public function hmo_Retainership(Visit $visit): int|float|null
    {
        return $visit->totalHmsBills() ? round((float)($visit->totalApprovedBills() / ($visit->totalHmsBills() - $visit->discount)) * 100) : null;
    }

    public function nhis(Visit $visit): int|float|null
    {
        return $visit->totalHmsBills() ? round((float)($visit->totalPayments() / ($visit->totalNhisBills() - $visit->discount)) * 100) : null;
    }
}