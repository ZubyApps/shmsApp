<?php

declare(strict_types=1);

namespace App\Enum;

enum PatientType: string
{
    case Regular    = 'Regular';
    case ANC        = 'ANC';
}
