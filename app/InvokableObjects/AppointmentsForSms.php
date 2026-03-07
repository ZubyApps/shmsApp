<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

use App\Jobs\SendAppointmentReminder;
use App\Models\Appointment;
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

        $appointments = Appointment::whereBetween('date', [$time2, $time1])->get();

        if ($appointments->isEmpty()){
            return;
        }

        $appointments->each(function ($appointment) use ($helperService) {
            if ($appointment?->patient?->canSms() && !$helperService->isAirtel($appointment?->patient->phone)) {
                SendAppointmentReminder::dispatch($appointment);
            }
        });

      }, 2);
   }
}