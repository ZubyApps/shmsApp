<?php

declare(strict_types=1);

namespace App\Enum;

enum CommunicationType: int {
    case SMS = 1;
    case WHATSAPP = 2;
    case EMAIL = 3;
}
