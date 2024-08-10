<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

use App\Models\ShiftReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanUpTables
{
    public function __invoke()
   {
       DB::transaction(function () {   
        
        $date = (new Carbon())->subMonths(2);

        $shiftReports = ShiftReport::where('created_at', '<', $date.' 00:00:00')->get();
        
        if (!$shiftReports){
            return;
        }

        foreach($shiftReports as $report) {
            $report->destroy($report->id);
        }
      }, 2);
      Log::info('Shift report table cleaned');
   }
}