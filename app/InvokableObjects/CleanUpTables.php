<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

class CleanUpTables
{
    public function __invoke()
   {
        (new ShiftReportTable)();
        (new AppointmentTable)();
   }
}