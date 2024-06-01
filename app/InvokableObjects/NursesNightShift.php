<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

use App\Models\ShiftPerformance;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;

class NursesNightShift
{
   public function __invoke()
   {
      (new CloseNursesShift)();

      $date = CarbonImmutable::now();

      ShiftPerformance::create([
         'department'   => 'Nurse',
         'shift'        => 'Night Shift',
         'shift_start'  => $date->format('Y-m-d').' 20:00:01',
         'shift_end'    => $date->addDay()->format('Y-m-d').' 08:00:00'
         ]);

      Log::info('Night Shift created');

   }
}