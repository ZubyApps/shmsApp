<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

use App\Jobs\SendAppointmentReminder;
use App\Models\Appointment;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class AppointmentsForSms
{
    public function __invoke()
   {
       DB::transaction(function () {   
        
        $time1 = (new CarbonImmutable())->addHours(4);
        $time2 = $time1->subSeconds(59);

        $appointments = Appointment::whereBetween('date', [$time2, $time1])->get();

        if ($appointments->isEmpty()){
            return;
        }

        // foreach($appointments as $appointment) {
        //     if ($appointment->patient->sms){
        //         SendAppointmentReminder::dispatch($appointment);
        //         info('appointments in', ['patient' => $appointment->patient->first_name, 'doctor' => $appointment->doctor->username, 'sms' => $appointment->patient->sms]);
        //     }
        // }

        $appointments->each(function ($appointment) {
            if ($appointment->patient->sms) {
                SendAppointmentReminder::dispatch($appointment);
                info('appointments in', ['patient' => $appointment->patient->first_name, 'doctor' => $appointment->doctor->username, 'sms' => $appointment->patient->sms]);
            }
        });

      }, 2);
   }
}