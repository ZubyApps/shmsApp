<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

class ContinuousSmsing
{
    public function __invoke()
   {
        (new MedicationsForSms)();
        (new AppointmentsForSms)();
   }
}