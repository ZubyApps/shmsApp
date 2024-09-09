<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

use App\Models\ShiftReport;
use Illuminate\Support\Facades\DB;

class ShiftReportNotifier
{
   public function __invoke()
   {
      DB::transaction(function () {   
         $nursesShiftReport = ShiftReport::where('department', 'nurses')->whereNull('viewed_at_2')->get();
         
         if ($nursesShiftReport->isEmpty()){
            return;
         }
   
         foreach($nursesShiftReport as $report) {
            $report->update(['notify' => true]);
         }
      }, 2);

   }
}