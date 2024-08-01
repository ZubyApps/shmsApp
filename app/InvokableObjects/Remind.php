<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

use App\Models\Reminder;
use App\Models\ShiftPerformance;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Remind
{
   public function __invoke()
   {

      DB::transaction(function () {   
         $date = CarbonImmutable::now();
         $reminders = Reminder::whereNull('confirmed_paid')->whereNull('final_reminder')->get();
         
         if (!$reminders){
            return;
         }
   
         foreach($reminders as $reminder) {
            $maxDays    = $reminder->max_days;
            $diffInDays = $date->diffInDays($reminder->set_from);
            $daysToCheck = round($maxDays/2);

            if ($diffInDays >= $daysToCheck){
               if (!$reminder->first_reminder){
                     $reminder->update(['remind' => true]);
                  }

               if ($diffInDays >= ($daysToCheck + ($daysToCheck/2))){
                  if (!$reminder->second_reminder){
                     $reminder->update(['remind' => true]);
                  }
               }

               if ($diffInDays >= ($maxDays-5)){
                  if (!$reminder->final_reminder){
                     $reminder->update(['remind' => true]);
                  }
               }
            }
            Log::info('Reminder details', [$diffInDays, $maxDays, $reminder->visit_id]);
            if ($diffInDays > $maxDays){
               if ($reminder->visit_id){
                  $reminder->visit->patient->update(['flag' => true]);
               } else {
                  $reminder->sponsor->update(['flag' => true]);
               }
            }
         }
      }, 2);

   }
}