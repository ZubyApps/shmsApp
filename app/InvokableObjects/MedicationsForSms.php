<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

use App\Models\MedicationChart;
use App\Notifications\MedicationNotifier;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
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

        $medications = MedicationChart::where(function(Builder $query) {
                            $query->whereRelation('visit', 'admission_status', '=', 'Outpatient')
                            ->orWhere(function(Builder $query) {
                                $query->whereRelation('visit', 'admission_status', '=', 'Inpatient')
                                ->whereRelation('visit', 'discharge_reason', '!=', null);
                            });
                        })
                        ->whereRelation('visit.patient', 'sms', '=', true)
                        ->whereRelation('prescription', 'discontinued', '=', false)
                        ->whereBetween('scheduled_time', [$time2, $time1])
                        ->whereNull('time_given')->get();

        if ($medications->isEmpty()){
            return;
        }

        foreach($medications as $medication) {
            $this->medicationNotifier->toSms($medication);
        }
      }, 2);
   }
}