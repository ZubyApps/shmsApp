<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

use App\Models\ShiftPerformance;

class NursesMorningShift
{
   public function __invoke()
   {
      ShiftPerformance::create([
         'department'   => 'Nurse',
         'shift'        => 'Morning Shift',
         ]);
   }
}