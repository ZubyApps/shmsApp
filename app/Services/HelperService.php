<?php

declare(strict_types = 1);

namespace App\Services;

use Carbon\Carbon;

class HelperService
{
    public function twoPartDiffInTimePast($date): String
    {
        return str_replace(['a', 'g', 'o'], '', (new Carbon($date))->diffForHumans(['other' => null, 'parts' => 2, 'short' => true]), );
    }

    public function twoPartDiffInTimeToCome($date): String
    {
        return str_replace(['now', 'from', 'o'], '', (new Carbon($date))->diffForHumans(['other' => null, 'parts' => 2, 'short' => true]), );
    }
}