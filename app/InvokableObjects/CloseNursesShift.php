<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

use App\Models\ShiftPerformance;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CloseNursesShift
{
   public function __invoke()
   {
      DB::transaction(function () {
         $nursesOnDuty = User::whereRelation('designation', 'designation', 'Nurse')->where('is_active', true)->get();
   
         $shiftPerformance = ShiftPerformance::where('department', 'Nurse')->where('is_closed', false)->orderBy('id', 'desc')->first();
         
         if (!$shiftPerformance){
            return;
        }
         $shiftPerformance->update([
            'is_closed' => true
            ]);
   
         foreach($nursesOnDuty as $nurse){
            $nurse->update(['is_active' => false]);
         }
      }, 2);
   }
}