<?php

declare(strict_types = 1);

namespace App\Services;

use Carbon\Carbon;
use App\Models\Ward;
use Carbon\CarbonImmutable;
use App\DataObjects\DataTableQueryParams;
use Illuminate\Database\Eloquent\Builder;

class HelperService
{
    public function twoPartDiffInTimePast($date): String
    {
        return str_replace(['a', 'g', 'o'], '', (new Carbon($date))->diffForHumans(['other' => null, 'parts' => 2, 'short' => true]), );
    }

    public function twoPartDiffInTimeToCome($date): String
    {
        return str_replace(['now', 'from', 'o', 'g', 'a'], '', (new Carbon($date))->diffForHumans(['other' => null, 'parts' => 2, 'short' => true]), );
    }
    
    public function twoPartDiffInTimeToCome1($date): String
    {
        return (new Carbon())->diffForHumans($date, ['other' => null, 'parts' => 2, 'short' => true], true);
    }

    public function flagExpired($date)
    {
        return new Carbon() > new Carbon($date);
    }

    public function nccTextTime()
    {
        $start = new CarbonImmutable('08:00:00');
        $end = $start->addHours(11);
        return Carbon::now()->between($start, $end);
    }

    public function displayWard(Ward $ward)
    {
        return $ward->short_name . '-Bed' . $ward->bed_number;
    }

    public function paginateQuery(Builder $query, DataTableQueryParams $params, string $orderBy = 'consulted', string $orderDir = 'desc')
    {
        return $query->orderBy($orderBy, $orderDir)
            ->paginate($params->length, '*', '', (($params->length + $params->start) / $params->length));
    }
}