<?php

declare(strict_types=1);

namespace App\Enum;

enum PayClass: string
{
    case Cash    = 'Cash';
    case Credit  = 'Credit';
}
