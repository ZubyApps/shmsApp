<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

use App\Models\ShiftReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShiftReportTable
{
   public function __invoke()
   {
    DB::transaction(function () {
        $date = (new Carbon())->subMonths(2)->startOfDay();

        $shiftReports = ShiftReport::where('created_at', '<', $date)->pluck('id');

        if ($shiftReports->isEmpty()) {
            return;
        }

        ShiftReport::destroy($shiftReports);

        Log::info('Shift report table cleaned');
    }, 2);
   }
}