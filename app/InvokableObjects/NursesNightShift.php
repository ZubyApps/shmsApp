<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

use App\Models\ShiftPerformance;

class NursesNightShift
{
   public function __invoke()
   {
      ShiftPerformance::create([
         'department'   => 'Nurse',
         'shift'        => 'Night Shift',
         ]);
   }
}