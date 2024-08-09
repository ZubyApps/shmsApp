<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

use App\Models\ShiftPerformance;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;

class NursesMorningShift
{
   public function __invoke()
   {
      (new CloseNursesShift)();

      $date = CarbonImmutable::now();

      ShiftPerformance::create([
         'department'   => 'Nurse',
         'shift'        => 'Morning Shift',
         'shift_start'  => $date->format('Y-m-d').' 08:00:01',
         'shift_end'    => $date->format('Y-m-d').' 14:00:00'
         ]);

      Log::info('Morning Shift created');

      (new ShiftReportNotifier)();
   }
}