<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppointmentTable
{
   public function __invoke()
   {
    DB::transaction(function () {
        $date = (new Carbon())->endOfDay();

        $appointmentIds = Appointment::where('date', '<', $date)->pluck('id');

        if ($appointmentIds->isEmpty()) {
            return;
        }

        Appointment::destroy($appointmentIds);

        Log::info('Appointment table cleaned');
    }, 2);
   }
}