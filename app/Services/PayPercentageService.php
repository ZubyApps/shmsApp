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
        return $visit->totalHmsBills() && $visit->totalHmsBills() != $visit->discount  ? round((float)($visit->totalPayments() / ($visit->totalHmsBills() - $visit->discount)) * 100, 2) : null;
    }

    public function hmo_Retainership(Visit $visit): int|float|null
    {
        return $visit->totalHmsBills() && $visit->totalHmsBills() != $visit->discount ? round((float)($visit->totalApprovedBills() / ($visit->totalHmsBills() - $visit->discount)) * 100) : null;
    }

    public function nhis(Visit $visit): int|float|null
    {
        // return $visit->totalHmsBills() && $visit->totalNhisBills() != $visit->discount ? round((float)($visit->totalPayments() / ($visit->totalNhisBills() - $visit->discount)) * 100, 2) : null;
        return $visit->totalHmsBills() && $visit->totalNhisBills() != $visit->discount ? round((float)(($visit->totalPaidPrescriptions() > $visit->totalPayments() ? $visit->totalPaidPrescriptions() : $visit->totalPayments()) / ($visit->totalNhisBills() - $visit->discount)) * 100, 2) : null;
    }
}