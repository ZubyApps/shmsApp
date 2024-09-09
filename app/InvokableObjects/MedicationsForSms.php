<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

use App\Models\MedicationChart;
use App\Models\ShiftReport;
use App\Notifications\MedicationNotifier;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MedicationsForSms
{
    public function __construct(private readonly MedicationNotifier $medicationNotifier)
    {
    }

    public function __invoke()
   {
       DB::transaction(function () {   
        
        $time1 = (new CarbonImmutable())->addHour();
        $time2 = $time1->subSeconds(59);

        $medications = MedicationChart::whereRelation('visit', 'admission_status', '=', 'Outpatient')
                        ->whereRelation('visit.patient', 'sms', '=', true)
                        ->whereBetween('scheduled_time', [$time2, $time1])->get();

        if ($medications->isEmpty()){
            return;
        }

        foreach($medications as $medication) {
            $this->medicationNotifier->toSms($medication);
            Log::info('Text sent to '.$medication?->visit->patient->first_name);
        }
      }, 2);
   }
}