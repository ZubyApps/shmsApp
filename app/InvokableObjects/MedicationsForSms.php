<?php

declare(strict_types = 1);

namespace App\InvokableObjects;

use App\Jobs\SendMedicationReminder;
use App\Models\MedicationChart;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

class MedicationsForSms
{
    public function __invoke()
   {
        $time1 = (new CarbonImmutable())->addHour();
        $time2 = $time1->subSeconds(59);

        $medications = MedicationChart::select('id', 'visit_id', 'scheduled_time')->with([
            'visit' => function($query){
                $query->select('id', 'patient_id')
                ->with(['patient:id,sms,first_name,phone']);
            }
        ])->where(function(Builder $query) {
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

        $medications->each(function ($medication) {
            if ($medication->visit->patient->sms) {
                SendMedicationReminder::dispatch(
                    $medication->visit->patient->first_name,
                    $medication->visit->patient->phone,
                    $medication->scheduled_time
                )->delay(5);
            }
        });
   }
}