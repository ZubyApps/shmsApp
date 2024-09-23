<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

use App\Models\Appointment;
use App\Notifications\AppointmentNotifier;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class AppointmentsForSms
{
    public function __construct(private readonly AppointmentNotifier $appointmentNotifier)
    {
    }

    public function __invoke()
   {
       DB::transaction(function () {   
        
        $time1 = (new CarbonImmutable())->addHours(4);
        $time2 = $time1->subSeconds(59);

        $appointments = Appointment::whereBetween('date', [$time2, $time1])->get();

        if ($appointments->isEmpty()){
            return;
        }

        foreach($appointments as $appointment) {
            $this->appointmentNotifier->toSms($appointment);
        }
      }, 2);
   }
}