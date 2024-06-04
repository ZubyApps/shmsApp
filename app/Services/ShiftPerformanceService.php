<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\MedicationChart;
use App\Models\Prescription;
use App\Models\ShiftPerformance;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

Class ShiftPerformanceService
{
    public function __construct(
        private readonly ShiftPerformance $shiftPerformance,
        private readonly Prescription $prescription,
        private readonly MedicationChart $medicationChart,
        private readonly Visit $visit
        )
    {
        
    }

    public function update()
    {
        return DB::transaction(function () {
            $shiftPerformance = $this->shiftPerformance->where('department', 'Nurse')->where('is_closed', false)->orderBy('id', 'desc')->first();
            $nursesOnDuty = User::whereRelation('designation', 'designation', 'Nurse')->where('is_active', true)->get();
            $nurses = [];
      
            foreach($nursesOnDuty as $nurse){
               $nurses[] = $nurse->username;
            }
            
            if (!$shiftPerformance){
                return;
            }

            $shiftPerformance->update([
                    'chart_rate'                => $this->chartRate($shiftPerformance),
                    'given_rate'                => $this->givenRate($shiftPerformance),
                    'first_med_res'             => $this->firstMedicationResolution($shiftPerformance),
                    'first_vitals_res'          => $this->firstVitalsignsResolution($shiftPerformance),
                    'medication_time'           => $this->medicationTime($shiftPerformance),
                    'inpatient_vitals_count'    => $this->inpatientsVitalsignsCount($shiftPerformance),
                    'outpatient_vitals_count'   => $this->outpatientssVitalsignsCount($shiftPerformance),
                ]);
    
                $shiftPerformance->update([
                    'performance'  => $this->getPerformance($shiftPerformance),
                    'staff'        => $nurses
                ]);
    
                $shiftPerformance->first_med_res    = $shiftPerformance->first_med_res ? CarbonInterval::seconds($shiftPerformance->first_med_res)->cascade()->forHumans() : null;
                $shiftPerformance->first_vitals_res = $shiftPerformance->first_vitals_res ? CarbonInterval::seconds($shiftPerformance->first_vitals_res)->cascade()->forHumans() : null;
                $shiftPerformance->medication_time  = $shiftPerformance->medication_time ? ($shiftPerformance->medication_time < 0 ? 'Many served before scheduled time': CarbonInterval::seconds($shiftPerformance->medication_time)->cascade()->forHumans()) : null;

            return response()->json(['shiftPerformance' => $shiftPerformance]);
        }, 2);
    }

    public function chartRate($shiftPerformance)
    {
        if ($shiftPerformance?->shift == 'Morning Shift'){
            $totalPrescriptions         = $this->prescription->where('chartable', true)->where('held', null)->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])->count();
            $totalPrescriptionsCharted  = $this->prescription->prescriptionsChartedPerShift($shiftPerformance, 'medicationCharts');

            return $totalPrescriptions ? $totalPrescriptionsCharted . '/' . $totalPrescriptions : null;
        }
        if ($shiftPerformance?->shift == 'Afternoon Shift'){
            $totalPrescriptions         = $this->prescription->where('chartable', true)->where('held', null)->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])->count();
            $totalPrescriptionsCharted  = $this->prescription->prescriptionsChartedPerShift($shiftPerformance, 'medicationCharts');

            return $totalPrescriptions ? $totalPrescriptionsCharted . '/' . $totalPrescriptions : null;
        }

        if ($shiftPerformance?->shift == 'Night Shift'){
            $totalPrescriptions         = $this->prescription->where('chartable', true)->where('held', null)->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])->count();
            $totalPrescriptionsCharted  = $this->prescription->prescriptionsChartedPerShift($shiftPerformance, 'medicationCharts');

            return $totalPrescriptions ? $totalPrescriptionsCharted . '/' . $totalPrescriptions : null; 
        }
    }

    public function givenRate($shiftPerformance)
    {
        if ($shiftPerformance?->shift == 'Morning Shift'){
            $totalPrescriptions         = $this->prescription->where('chartable', true)->where('held', null)->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])->count();
            $totalPrescriptionsStarted  = $this->prescription->prescriptionsGivenPerShift($shiftPerformance, 'medicationCharts');

            return $totalPrescriptions ? $totalPrescriptionsStarted . '/' . $totalPrescriptions : null; 

        }

        if ($shiftPerformance?->shift == 'Afternoon Shift'){
            $totalPrescriptions         = $this->prescription->where('chartable', true)->where('held', null)->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])->count();
            $totalPrescriptionsStarted  = $this->prescription->prescriptionsGivenPerShift($shiftPerformance, 'medicationCharts');

            return $totalPrescriptions ? $totalPrescriptionsStarted . '/' . $totalPrescriptions : null; 

        }

        if ($shiftPerformance?->shift == 'Night Shift'){
            $totalPrescriptions         = $this->prescription->where('chartable', true)->where('held', null)->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])->count();
            $totalPrescriptionsStarted  = $this->prescription->prescriptionsGivenPerShift($shiftPerformance, 'medicationCharts');

            return $totalPrescriptions ? $totalPrescriptionsStarted . '/' . $totalPrescriptions : null; 
        }
    }

    public function firstMedicationResolution($shiftPerformance)
    {
        $prescriptionsWithoutMc = $this->prescription->where('chartable', true)->where('held', null)->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])->whereDoesntHave('medicationCharts')->count();

        $prescriptionsWitMc = $this->prescription->where('chartable', true)->where('held', null)->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])->whereHas('medicationCharts')->count();

        $averageFMRTime = DB::table('prescriptions')
                    ->selectRaw('AVG(TIME_TO_SEC(TIMEDIFF(medication_charts.time_given, prescriptions.created_at))) AS averageFMRTime')
                    ->leftJoin('medication_charts', 'prescriptions.id', 'medication_charts.prescription_id')
                    ->where('medication_charts.dose_count', 1)
                    ->where('prescriptions.held', null)
                    ->whereBetween('prescriptions.created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])
                    ->get()->first()->averageFMRTime;
        
        return $prescriptionsWithoutMc > 0 || $prescriptionsWitMc > 0 ? $averageFMRTime : null;     
    }

    public function firstVitalsignsResolution($shiftPerformance)
    {
        $visitsWithoutVs    = $this->visit->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])->whereDoesntHave('vitalSigns')->count();

        $visitsWithVs       = $this->visit->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])->whereHas('vitalSigns')->count();

        $averageFVRTime = DB::table('visits')
                    ->selectRaw('AVG(TIME_TO_SEC(TIMEDIFF(first_vitalsigns, created_at))) AS averageFVRTime')
                    ->whereBetween('visits.created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])
                    ->get()->first()?->averageFVRTime;

        return $visitsWithoutVs > 0 || $visitsWithVs > 0 ? $averageFVRTime : null;        
    }

    public function medicationTime($shiftPerformance)
    {
        $medicatonsDueInShift = $this->medicationChart
                                ->whereBetween('scheduled_time', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])
                                ->orWhereBetween('time_given', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])
                                ->whereRelation('visit', 'admission_status', '!=', 'Outpatient')
                                ->count();

        $averageMedicationTimes = DB::table('medication_charts')
                                ->selectRaw('AVG(TIME_TO_SEC(TIMEDIFF(medication_charts.time_given, medication_charts.scheduled_time))) AS averageMedicationTime')
                                ->leftJoin('visits', 'visits.id', 'medication_charts.visit_id')
                                ->whereBetween('scheduled_time', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])
                                ->where('visits.admission_status', '!=', 'OutPatient')
                                // ->orWhere('visits.admission_status', null)
                                ->get()->first()->averageMedicationTime;

        $averageMedicationTime = $medicatonsDueInShift ? ($averageMedicationTimes ? $averageMedicationTimes : null) : null;

        return $medicatonsDueInShift > 0 ? $averageMedicationTime : null;        
    }

    public function inpatientsVitalsignsCount($shiftPerformance)
    {
        $visitsCount = $this->visit
                ->where(function (EloquentBuilder $query) {
                    $query->where('admission_status', '=', 'Inpatient')
                    ->orWhere('admission_status', '=', 'Observation');
                })
                ->where('doctor_done_by', null)
                ->count();

        $visitsVCount = $this->visit
                ->where(function (EloquentBuilder $query) {
                    $query->where('admission_status', '=', 'Inpatient')
                    ->orWhere('admission_status', '=', 'Observation');
                })
                ->where('doctor_done_by', '=', null)
                ->whereHas('vitalSigns', function ($query) use ($shiftPerformance) {
                            $query->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end]);
                    }, '>=', 3)->count();

        return $visitsCount ? $visitsVCount . '/' . $visitsCount : null;
    }

    public function outpatientssVitalsignsCount($shiftPerformance)
    {
        $visitsCount = $this->visit
                ->whereRelation('patient', 'patient_type', '!=', 'ANC')
                ->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])
                ->count();

        $visitsVCount = $this->visit
                ->whereRelation('patient', 'patient_type', '!=', 'ANC')
                ->whereHas('vitalSigns', function ($query) use ($shiftPerformance) {
                            $query->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end]);
                    }, '>=', 1
                )
                ->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])
                ->count();

        return $visitsCount ? $visitsVCount . '/' . $visitsCount : null;
    }

    public function secondsToPercent($seconds, $indicator)
    {
        if ($indicator == 'FMR'){
            return $seconds < 600 ? 100 : ($seconds > 600 && $seconds < 1200 ? 90 : ($seconds > 1200 && $seconds < 2400 ? 80 : ($seconds > 2400 && $seconds < 3600 ? 60 : ($seconds > 3600 && $seconds < 5400 ? 40 : 20))));
        }
        if ($indicator == 'FVR'){
            return $seconds < 300 ? 100 : ($seconds > 300 && $seconds < 600 ? 90 : ($seconds > 600 && $seconds < 900 ? 80 : ($seconds > 900 && $seconds < 1200 ? 70 : ($seconds > 1200 && $seconds < 1500 ? 60 : ($seconds > 1500 && $seconds < 2400 ? 40 : 20)))));
        }
        if ($indicator == 'MT'){
            return $seconds < 120 ? 100 : ($seconds > 120 && $seconds < 300 ? 80 : ($seconds > 300 && $seconds < 600 ? 70 : ($seconds > 600 && $seconds < 900 ? 50 : ($seconds > 900 && $seconds < 1200 ? 40 : 20))));
        }
    }

    public function getPerformance($shiftPerformance)
    {
        $totalPoints = 0;

        $convertChartRate = $shiftPerformance->chart_rate === null ? null : 
                            ($this->percentFromStringFraction($shiftPerformance->chart_rate) / 100) * 20 ; 
                            $shiftPerformance->chart_rate === null ? '' : $totalPoints++;

        $convertGivenRate = $shiftPerformance->given_rate === null ? 
                            null: ($this->percentFromStringFraction($shiftPerformance->given_rate) / 100) * 20; 
                            $shiftPerformance->given_rate === null ? '': $totalPoints++;

        $convertFirstMedRes =   $shiftPerformance->first_med_res === null ? null :
                                ($this->secondsToPercent($shiftPerformance->first_med_res, 'FMR') /100 ) * 20; 
                                $shiftPerformance->first_med_res === null ? '' : $totalPoints++;

        $convertFirstVitalsRes  =   $shiftPerformance->first_vitals_res === null ? null :
                                    ($this->secondsToPercent($shiftPerformance->first_vitals_res, 'FVR') / 100) * 20; 
                                    $shiftPerformance->first_vitals_res === null ? '' : $totalPoints++;

        $convertMedicationTime =    $shiftPerformance->medication_time === null ? null : 
                                    ($this->secondsToPercent($shiftPerformance->medication_time, 'MT') / 100) * 20; 
                                    $shiftPerformance->medication_time === null ? '' : $totalPoints++;

        $convertInPsVC  =   $shiftPerformance->inpatient_vitals_count === null ? null : 
                            ($this->percentFromStringFraction($shiftPerformance->inpatient_vitals_count) / 100) * 20; 
                            $shiftPerformance->inpatient_vitals_count === null ? '' : $totalPoints++;

        $convertOutPsVC =   $shiftPerformance->outpatient_vitals_count === null ? null : 
                            ($this->percentFromStringFraction($shiftPerformance->outpatient_vitals_count) / 100) * 20; 
                            $shiftPerformance->outpatient_vitals_count === null ? '' : $totalPoints++;

        $preformance = $totalPoints ? ($convertChartRate + $convertGivenRate + $convertFirstMedRes + $convertFirstVitalsRes + $convertMedicationTime + $convertInPsVC + $convertOutPsVC)/($totalPoints*20) * 100 : 0;
            
        return round($preformance, 1);
    }

    public function percentFromStringFraction($fraction){
        $exploded = explode('/', $fraction);
        return round($exploded[0] / $exploded[1] * 100, 1);
    }

    public function getShiftPerformance(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        return $this->shiftPerformance
                    ->where('department', $data->department)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getNursesShiftPerformanceTransformer(): callable
    {
       return  function (ShiftPerformance $shiftPerformance) {
            return [
                'department'            => $shiftPerformance->department,
                'shift'                 => $shiftPerformance->shift,
                'start'                 => (new Carbon($shiftPerformance->shift_start))->format('d/M/y g:ia'),
                'end'                   => (new Carbon($shiftPerformance->shift_end))->format('d/M/y g:ia'),
                'chartRate'             => $shiftPerformance->chart_rate,
                'givenRate'             => $shiftPerformance->given_rate,
                'firstMedRes'           => $shiftPerformance->first_med_res,
                'firstVitalsRes'        => $shiftPerformance->first_vitals_res,
                'medicationTime'        => $shiftPerformance->medication_time,
                'intpatientVitalsCount' => $shiftPerformance->inpatient_vitals_count,
                'outpatientVitalsCount' => $shiftPerformance->outpatient_vitals_count,
                'performance'           => $shiftPerformance->performance,
                'staff'                 => $shiftPerformance->staff,
                'closed'                => $shiftPerformance->is_closed,
            ];
       };
    }
}