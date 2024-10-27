<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

use App\Models\ShiftPerformance;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;

class NursesAfternoonShift
{
   public function __invoke()
   {
      Log::info('this ran in afternoon shift');
      (new CloseNursesShift)();
      
      $date = CarbonImmutable::now();
      
      ShiftPerformance::create([
         'department'   => 'Nurse',
         'shift'        => 'Afternoon Shift',
         'shift_start'  => $date->format('Y-m-d').' 14:00:01',
         'shift_end'    => $date->format('Y-m-d').' 19:30:00'
         ]);

      Log::info('Afternoon Shift created');
      
      (new ShiftReportNotifier)();
   }
}