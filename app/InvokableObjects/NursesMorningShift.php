<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

use App\Models\ShiftPerformance;
use Carbon\CarbonImmutable;

class NursesMorningShift
{
   public function __invoke()
   {
      $date = CarbonImmutable::now();

      ShiftPerformance::create([
         'department'   => 'Nurse',
         'shift'        => 'Morning Shift',
         'shift_start'  => $date->format('Y-m-d').' 08:00:01',
         'shift_end'    => $date->format('Y-m-d').' 14:00:00'
         ]);
   }
}