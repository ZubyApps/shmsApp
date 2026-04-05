<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

// use App\Jobs\SendAppointmentReminder;
use App\Models\Appointment;
use App\Notifications\AppointmentNotification;
use App\Services\HelperService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class AppointmentsForSms
{
    public function __invoke()
   {
       $helperService = new HelperService();
       
       $isTextTime  = $helperService->nccTextTime();
    
       if (!$isTextTime){
           return;
       }

       DB::transaction(function () use ($helperService) {   
       
        $time1 = (new CarbonImmutable())->addHours(2);
        $time2 = $time1->subSeconds(59);

        $appointments = Appointment::whereBetween('date', [$time2, $time1])->with(['patient', 'doctor'])->get();

        if ($appointments->isEmpty()){
            return;
        }

        $appointments->each(function ($appointment) use ($helperService) {
            $patient = $appointment->patient;
            $doctor = $appointment->doctor;

            //Notify Patient
            if ($helperService->shouldNotify($patient?->phone, $patient)) {
                $patient->notify(new AppointmentNotification($appointment, 'patient'));
            }

            //Notify Doctor
            if ($helperService->shouldNotify($doctor?->phone_number)) {
                $doctor->notify(new AppointmentNotification($appointment, 'doctor'));
            }

            // if ($appointment?->patient?->canSms() && !$helperService->isAirtel($appointment?->patient->phone)) {
            //     SendAppointmentReminder::dispatch($appointment);
            // }
        });

      }, 2);
   }
}