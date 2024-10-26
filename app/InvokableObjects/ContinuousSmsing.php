<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

use App\Notifications\AppointmentNotifier;
use App\Notifications\MedicationNotifier;
use App\Services\ChurchPlusSmsService;

class ContinuousSmsing
{
    public function __invoke()
   {
     //    (new MedicationsForSms(new MedicationNotifier(new ChurchPlusSmsService())))();
     //    (new AppointmentsForSms(new AppointmentNotifier(new ChurchPlusSmsService())))();
   }
}