<?php

declare(strict_types=1);

namespace App\Enum;

enum VerificationStatus: string
{
    case Verified  = 'Verified';
    case Pending   = 'Penidng';
    case Exponged  = 'Exponged';
}
