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
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
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
            $nurses     = [];
      
            foreach($nursesOnDuty as $nurse){
               $nurses[] = $nurse->username;
            }
            
            if (!$shiftPerformance){
                return;
            }

            $chartRate          = $this->chartRate($shiftPerformance);
            $givenRate          = $this->givenRate($shiftPerformance);
            $inpatientsVitals   = $this->inpatientsVitalsignsCount($shiftPerformance);
            $outpatientsVitals  = $this->outpatientssVitalsignsCount($shiftPerformance);

            $shiftPerformance->update([
                    'chart_rate'                => $chartRate ? $chartRate['totalPrescriptionsCharted'] . '/' . $chartRate['totalPrescriptions'] : $chartRate,
                    'given_rate'                => $givenRate ? $givenRate['totalPrescriptionsStarted'] . '/' . $givenRate['totalPrescriptions'] : $givenRate,
                    'first_med_res'             => $this->firstMedicationResolution($shiftPerformance),
                    'first_vitals_res'          => $this->firstVitalsignsResolution($shiftPerformance),
                    'medication_time'           => $this->medicationTime($shiftPerformance),
                    'inpatient_vitals_count'    => $inpatientsVitals ? $inpatientsVitals['visitsVCount'] . '/' . $inpatientsVitals['visitsCount'] : $inpatientsVitals,
                    'outpatient_vitals_count'   => $outpatientsVitals ? $outpatientsVitals['visitsVCount'] . '/' . $outpatientsVitals['visitsCount'] : $outpatientsVitals,
                ]);
    
                $shiftPerformance->update([
                    'performance'  => $this->getPerformance($shiftPerformance),
                    'staff'        => $nurses
                ]);
    
                $shiftPerformance->first_med_res    = $shiftPerformance->first_med_res ? CarbonInterval::seconds($shiftPerformance->first_med_res)->cascade()->forHumans() : null;
                $shiftPerformance->first_vitals_res = $shiftPerformance->first_vitals_res ? CarbonInterval::seconds($shiftPerformance->first_vitals_res)->cascade()->forHumans() : null;
                $shiftPerformance->medication_time  = $shiftPerformance->medication_time ? ($shiftPerformance->medication_time < 0 ? 'Many served on time': CarbonInterval::seconds($shiftPerformance->medication_time)->cascade()->forHumans()) : null;
                $details = ['notCharted' => $chartRate ? $chartRate['notChartedUnique'] : '', 'notGiven' => $givenRate ? $givenRate['notStartedUnique'] : '', 'inpatientsNoV' =>  $inpatientsVitals ? $inpatientsVitals['visitsNoVitals'] : '', 'outpatientsNoV' => $outpatientsVitals ? $outpatientsVitals['visitsNoVitals'] : ''];

            return response()->json(['shiftPerformance' => $shiftPerformance, 'details' => $details ? $details : '']);
        }, 2);
    }

    public function chartRate($shiftPerformance)
    {
        $shiftEnd = new Carbon($shiftPerformance->shift_end);
        $shiftEndTimer = $shiftEnd->subMinutes(20);
        $notCharted = [];

        $totalPrescriptions = $this->prescription
                                    ->where('chartable', true)
                                    ->where('discontinued', false)
                                    ->where('held', null)
                                    ->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
                                    ->count();

        $totalPrescriptionsCharted      = $this->prescription->prescriptionsChartedPerShift($shiftPerformance, 'medicationCharts');
        $totalPrescriptionsNotCharted   = $this->prescription->prescriptionsNotChartedPerShift($shiftPerformance, 'medicationCharts');

        foreach($totalPrescriptionsNotCharted as $NotChartedPrescription){
            array_push($notCharted, $NotChartedPrescription->visit->patient->card_no . ' ' . $NotChartedPrescription->visit->patient->first_name);
        }

        $notChartedUnique = array_values(array_unique($notCharted));

        $all = new Collection(['totalPrescriptions' => $totalPrescriptions, 'totalPrescriptionsCharted' => $totalPrescriptionsCharted, 'notChartedUnique' => $notChartedUnique]);

        return $totalPrescriptions ? $all : null;
    }

    public function givenRate($shiftPerformance)
    {
        $shiftEnd = new Carbon($shiftPerformance->shift_end);
        $shiftEndTimer = $shiftEnd->subMinutes(20);
        $notGiven = [];

        $totalPrescriptions         = $this->prescription
                                            ->where('chartable', true)
                                            ->where('discontinued', false)
                                            ->where('held', null)
                                            ->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
                                            ->count();

            $totalPrescriptionsStarted      = $this->prescription->prescriptionsGivenPerShift($shiftPerformance, 'medicationCharts');
            $totalPrescriptionsNotStarted   = $this->prescription->prescriptionsNotGivenPerShift($shiftPerformance, 'medicationCharts');

            foreach($totalPrescriptionsNotStarted as $NotStartedPrescription){
                array_push($notGiven, $NotStartedPrescription->visit->patient->card_no . ' ' . $NotStartedPrescription->visit->patient->first_name);
            }
    
            $notStartedUnique = array_values(array_unique($notGiven));
    
            $all = new Collection(['totalPrescriptions' => $totalPrescriptions, 'totalPrescriptionsStarted' => $totalPrescriptionsStarted, 'notStartedUnique' => $notStartedUnique]);
    
            return $totalPrescriptions ? $all : null;
    }

    public function firstMedicationResolution($shiftPerformance)
    {
        $shiftEnd = new Carbon($shiftPerformance->shift_end);
        $shiftEndTimer = $shiftEnd->subMinutes(20);

        $prescriptionsWithoutMc = $this->prescription
                                        ->where('chartable', true)
                                        ->where('discontinued', false)
                                        ->where('held', null)
                                        ->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
                                        ->whereDoesntHave('medicationCharts')->count();

        $prescriptionsWithMc    = $this->prescription
                                        ->where('chartable', true)
                                        ->where('discontinued', false)
                                        ->where('held', null)
                                        ->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
                                        ->whereHas('medicationCharts')->count();

        $averageFMRTime = DB::table('prescriptions')
                            ->selectRaw('AVG(TIME_TO_SEC(TIMEDIFF(medication_charts.time_given, prescriptions.created_at))) AS averageFMRTime')
                            ->leftJoin('medication_charts', 'prescriptions.id', 'medication_charts.prescription_id')
                            ->where('medication_charts.dose_count', 1)
                            ->where('prescriptions.held', null)
                            ->where('prescriptions.discontinued', false)
                            ->whereBetween('prescriptions.created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
                            ->get()->first()->averageFMRTime;
        
        return $prescriptionsWithoutMc > 0 || $prescriptionsWithMc > 0 ? $averageFMRTime : null;     
    }

    public function firstVitalsignsResolution($shiftPerformance)
    {
        $shiftEnd = new Carbon($shiftPerformance->shift_end);
        $shiftEndTimer = $shiftEnd->subMinutes(10);

        $visitsWithoutVs    = $this->visit
                                ->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
                                ->whereDoesntHave('vitalSigns')
                                ->count();

        $visitsWithVs       = $this->visit
                                ->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
                                ->whereHas('vitalSigns')
                                ->count();

        $averageFVRTime = DB::table('visits')
                    ->selectRaw('AVG(TIME_TO_SEC(TIMEDIFF(first_vitalsigns, created_at))) AS averageFVRTime')
                    ->whereBetween('visits.created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
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
                                ->get()->first()->averageMedicationTime;

        $averageMedicationTime = $medicatonsDueInShift ? ($averageMedicationTimes ? $averageMedicationTimes : null) : null;

        return $medicatonsDueInShift > 0 ? $averageMedicationTime : null;        
    }
    
    public function inpatientsVitalsignsCount($shiftPerformance)
    {
        $shiftStart         = new CarbonImmutable($shiftPerformance->shift_start);
        $shiftStartTimer    = $shiftStart->addHour();
        $count              = 2;
        $noVitals           = [];

        if ($shiftPerformance->shift == 'Night Shift'){
            $shiftStartTimer = $shiftStart->addHours(3);
            $count = 3;
        }

        $visitsCount = $this->visit
                ->where('created_at', '<', $shiftStartTimer)
                ->where(function (EloquentBuilder $query) {
                    $query->where('admission_status', '=', 'Inpatient')
                    ->orWhere('admission_status', '=', 'Observation');
                })
                ->where('doctor_done_by', null)
                ->count();

        $visitsVCount = $this->visit
                ->where('created_at', '<', $shiftStartTimer)
                ->where(function (EloquentBuilder $query) {
                    $query->where('admission_status', '=', 'Inpatient')
                    ->orWhere('admission_status', '=', 'Observation');
                })
                ->where('doctor_done_by', '=', null)
                ->whereHas('vitalSigns', function ($query) use ($shiftPerformance) {
                            $query->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end]);
                    }, '>=', $count)->count();

        $visitsNoVitals = $this->visit
                ->where('created_at', '<', $shiftStartTimer)
                ->where(function (EloquentBuilder $query) {
                    $query->where('admission_status', '=', 'Inpatient')
                    ->orWhere('admission_status', '=', 'Observation');
                })
                ->where('doctor_done_by', '=', null)
                ->whereHas('vitalSigns', function ($query) use ($shiftPerformance) {
                            $query->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end]);
                    }, '<', $count)->get();

        foreach($visitsNoVitals as $visitNoVitals){
            // array_push($noVitals, $visitNoVitals->patient->card_no . ' ' . $visitNoVitals->patient->first_name . ' ' . $visitNoVitals->ward . '-' . $visitNoVitals->bed_no);
            array_push($noVitals, $visitNoVitals->patient->card_no . ' ' . $visitNoVitals->patient->first_name);
        }

        $all = new Collection(['visitsCount' => $visitsCount, 'visitsVCount' => $visitsVCount, 'visitsNoVitals' => $noVitals]);

        return $visitsCount ? $all : null;
    }

    public function outpatientssVitalsignsCount($shiftPerformance)
    {
        $shiftEnd      = new Carbon($shiftPerformance->shift_end);
        $shiftEndTimer = $shiftEnd->subMinutes(10);
        $noVitals      = [];

        $visitsCount = $this->visit
                ->whereRelation('patient', 'patient_type', '!=', 'ANC')
                ->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
                ->count();

        $visitsVCount = $this->visit
                ->whereRelation('patient', 'patient_type', '!=', 'ANC')
                ->whereHas('vitalSigns', function ($query) use ($shiftPerformance, $shiftEndTimer) {
                            $query->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftEndTimer]);
                    }, '>=', 1
                )
                ->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
                ->count();

        $visitsNoVitals = $this->visit
                ->whereRelation('patient', 'patient_type', '!=', 'ANC')
                ->whereDoesntHave('vitalSigns', function ($query) use ($shiftPerformance, $shiftEndTimer) {
                            $query->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftEndTimer]);
                    })
                ->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
                ->get();

        foreach($visitsNoVitals as $visitNoVitals){
            array_push($noVitals, $visitNoVitals->patient->card_no . ' ' . $visitNoVitals->patient->first_name);
        }

        $all = new Collection(['visitsCount' => $visitsCount, 'visitsVCount' => $visitsVCount, 'visitsNoVitals' => $noVitals]);

        return $visitsCount ? $all : null;

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

        if (! empty($params->searchTerm)) {

            if ($data->date){
                $date = new CarbonImmutable($data->date);
                return $this->shiftPerformance
                    ->where('department', $data->department)
                    ->where('performance', '>', $params->searchTerm )
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if ($data->startDate && $data->endDate){
                return $this->shiftPerformance
                ->where('department', $data->department)
                ->where('performance', '>', $params->searchTerm )
                ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $this->shiftPerformance
                    ->where('department', $data->department)
                    ->where(function (EloquentBuilder $query) use($params) {
                        $query->orWhere('performance', '>', $params->searchTerm );
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->date){
            $date = new CarbonImmutable($data->date);
            return $this->shiftPerformance
                ->where('department', $data->department)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->startDate && $data->endDate){
            return $this->shiftPerformance
            ->where('department', $data->department)
            ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
            ->orderBy($orderBy, $orderDir)
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->shiftPerformance
                    ->where('department', $data->department)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getNursesShiftPerformanceTransformer(): callable
    {
       return  function (ShiftPerformance $shiftPerformance) {
            return [
                'id'                    => $shiftPerformance->id,
                'department'            => $shiftPerformance->department,
                'shift'                 => $shiftPerformance->shift,
                'start'                 => (new Carbon($shiftPerformance->shift_start))->format('d/M/y g:ia'),
                'end'                   => (new Carbon($shiftPerformance->shift_end))->format('d/M/y g:ia'),
                'chartRate'             => $shiftPerformance->chart_rate,
                'givenRate'             => $shiftPerformance->given_rate,
                'firstMedRes'           => $shiftPerformance->first_med_res ? CarbonInterval::seconds($shiftPerformance->first_med_res)->cascade()->forHumans() : null,
                'firstVitalsRes'        => $shiftPerformance->first_vitals_res ? CarbonInterval::seconds($shiftPerformance->first_vitals_res)->cascade()->forHumans() : null,
                'medicationTime'        => $shiftPerformance->medication_time ? ($shiftPerformance->medication_time < 0 ? 'Many served on time': CarbonInterval::seconds($shiftPerformance->medication_time)->cascade()->forHumans()) : null,
                'intpatientVitalsCount' => $shiftPerformance->inpatient_vitals_count,
                'outpatientVitalsCount' => $shiftPerformance->outpatient_vitals_count,
                'performance'           => $shiftPerformance->performance,
                'staff'                 => $shiftPerformance->staff,
                'closed'                => $shiftPerformance->is_closed,
            ];
       };
    }

    public function updateStaff(Request $data, ShiftPerformance $shiftPerformance)
    {
        return $shiftPerformance->update([
            'staff' => $data->staff
        ]);
    }
}