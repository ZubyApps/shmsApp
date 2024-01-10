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
        return $visit->totalBills() ? round((float)($visit->totalPayments() / ($visit->totalBills() - $visit->discount)) * 100) : null;
    }

    public function hmo_Retainership(Visit $visit): int|float|null
    {
        return $visit->totalBills() ? round((float)($visit->totalApprovedBills() / ($visit->totalBills() - $visit->discount)) * 100) : null;
    }

    public function nhis(Visit $visit): int|float|null
    {
        return $visit->totalBills() ? round((float)($visit->totalPayments() / (($visit->totalBills() - $visit->discount)/10)) * 100) : null;
    }
}