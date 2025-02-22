<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

use App\Models\Reminder;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class Remind
{
   public function __invoke()
   {

      // DB::transaction(function () {   
      //    $date = CarbonImmutable::now();
      //    $reminders = Reminder::whereNull('confirmed_paid')->whereNull('final_reminder')->get();
         
      //    if ($reminders->isEmpty()){
      //       return;
      //    }
   
      //    foreach($reminders as $reminder) {
      //       $maxDays    = $reminder->max_days;
      //       $diffInDays = $date->diffInDays($reminder->set_from);
      //       $daysToCheck = round($maxDays/2);

      //       if ($diffInDays >= $daysToCheck){
      //          if (!$reminder->first_reminder){
      //                $reminder->update(['remind' => true]);
      //             }

      //          if ($diffInDays >= ($daysToCheck + ($daysToCheck/2))){
      //             if (!$reminder->second_reminder){
      //                $reminder->update(['remind' => true]);
      //             }
      //          }

      //          if ($diffInDays >= ($maxDays-5)){
      //             if (!$reminder->final_reminder){
      //                $reminder->update(['remind' => true]);
      //             }
      //          }
      //       }

      //       if ($diffInDays > $maxDays){
      //          if ($reminder->visit_id){
      //             $reminder->visit->patient->update(['flag' => true, 'flag_reason' => 'Failed to keep the promise to pay bill']);
      //          } else {
      //             $reminder->sponsor->update(['flag' => true]);
      //          }
      //       }
      //    }
      // }, 2);

      DB::transaction(function () {
         $date = CarbonImmutable::now();
         $reminders = Reminder::whereNull('confirmed_paid')->whereNull('final_reminder')->get();

         if ($reminders->isEmpty()) {
             return;
         }

         $remindersToUpdate = [];
         $patientsToUpdate = [];
         $sponsorsToUpdate = [];

         foreach ($reminders as $reminder) {
             $maxDays = $reminder->max_days;
             $diffInDays = $date->diffInDays($reminder->set_from);
             $daysToCheck = round($maxDays / 2);

             if ($diffInDays >= $daysToCheck && !$reminder->first_reminder) {
                 $remindersToUpdate[] = $reminder->id;
             }

             if ($diffInDays >= ($daysToCheck + ($daysToCheck / 2)) && !$reminder->second_reminder) {
                 $remindersToUpdate[] = $reminder->id;
             }

             if ($diffInDays >= ($maxDays - 5) && !$reminder->final_reminder) {
                 $remindersToUpdate[] = $reminder->id;
             }

             if ($diffInDays > $maxDays) {
                 if ($reminder->visit_id) {
                     $patientsToUpdate[] = $reminder->visit->patient->id;
                 } else {
                     $sponsorsToUpdate[] = $reminder->sponsor->id;
                 }
             }
         }

         if (!empty($remindersToUpdate)) {
             Reminder::whereIn('id', $remindersToUpdate)->update(['remind' => true]);
         }

         if (!empty($patientsToUpdate)) {
             DB::table('patients')->whereIn('id', $patientsToUpdate)->update(['flag' => true, 'flag_reason' => 'Failed to keep the promise to pay bill']);
         }

         if (!empty($sponsorsToUpdate)) {
             DB::table('sponsors')->whereIn('id', $sponsorsToUpdate)->update(['flag' => true]);
         }
     }, 2);
   }
}