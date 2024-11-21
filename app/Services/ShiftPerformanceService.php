<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\MedicationChart;
use App\Models\NursingChart;
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
        private readonly NursingChart $nursingChart,
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

            $injectablesChartRate   = $this->injectablesChartRate($shiftPerformance);
            $othersChartRate        = $this->othersChartRate($shiftPerformance);
            $injectablesGivenRate   = $this->injectablesGivenRate($shiftPerformance);
            $othersDoneRate         = $this->othersDoneRate($shiftPerformance);
            $inpatientsVitals       = $this->inpatientsVitalsignsCount($shiftPerformance);
            $outpatientsVitals      = $this->outpatientssVitalsignsCount($shiftPerformance);

            $shiftPerformance->update([
                    'injectables_chart_rate'    => $injectablesChartRate ? $injectablesChartRate['totalInjectablePrescriptionsCharted'] . '/' . $injectablesChartRate['totalInjectablePrescriptions'] : $injectablesChartRate,
                    'others_chart_rate'         => $othersChartRate ? $othersChartRate['totalOtherPrescriptionsCharted'] . '/' . $othersChartRate['totalOtherPrescriptions'] : $othersChartRate,
                    'injectables_given_rate'    => $injectablesGivenRate ? $injectablesGivenRate['totalInjectablePrescriptionsStarted'] . '/' . $injectablesGivenRate['totalInjectablePrescriptions'] : $injectablesGivenRate,
                    'others_done_rate'          => $othersDoneRate ? $othersDoneRate['totalOtherPrescriptionsStarted'] . '/' . $othersDoneRate['totalOtherPrescriptions'] : $othersDoneRate,
                    'first_med_res'             => $this->firstMedicationResolution($shiftPerformance),
                    'first_serv_res'            => $this->firstServicesResolution($shiftPerformance),
                    'first_vitals_res'          => $this->firstVitalsignsResolution($shiftPerformance),
                    'medication_time'           => $this->medicationTime($shiftPerformance),
                    'service_time'              => $this->serviceTime($shiftPerformance),
                    'inpatient_vitals_count'    => $inpatientsVitals ? $inpatientsVitals['visitsVCount'] . '/' . $inpatientsVitals['visitsCount'] : $inpatientsVitals,
                    'outpatient_vitals_count'   => $outpatientsVitals ? $outpatientsVitals['visitsVCount'] . '/' . $outpatientsVitals['visitsCount'] : $outpatientsVitals,
                ]);
    
                $shiftPerformance->update([
                    'performance'  => $this->getPerformance($shiftPerformance),
                    'staff'        => $nurses
                ]);
    
                $shiftPerformance->first_med_res    = $shiftPerformance->first_med_res ? CarbonInterval::seconds($shiftPerformance->first_med_res)->cascade()->forHumans() : null;
                $shiftPerformance->first_serv_res    = $shiftPerformance->first_serv_res ? CarbonInterval::seconds($shiftPerformance->first_serv_res)->cascade()->forHumans() : null;
                $shiftPerformance->first_vitals_res = $shiftPerformance->first_vitals_res ? CarbonInterval::seconds($shiftPerformance->first_vitals_res)->cascade()->forHumans() : null;
                $shiftPerformance->medication_time  = $shiftPerformance->medication_time ? ($shiftPerformance->medication_time < 0 ? 'Many served on time': CarbonInterval::seconds($shiftPerformance->medication_time)->cascade()->forHumans()) : null;
                $shiftPerformance->service_time  = $shiftPerformance->service_time ? ($shiftPerformance->service_time < 0 ? 'Many served on time': CarbonInterval::seconds($shiftPerformance->service_time)->cascade()->forHumans()) : null;
                $details = ['notChartedInjectables' => $injectablesChartRate ? $injectablesChartRate['notChartedUniqueInjectables'] : '', 'notChartedOthers' => $othersChartRate ? $othersChartRate['notChartedUniqueOthers'] : '', 'notStartedInjectables' => $injectablesGivenRate ? $injectablesGivenRate['notStartedUniqueInjectables'] : '', 'notStartedOthers' => $othersDoneRate ? $othersDoneRate['notStartedUniqueOthers'] : '', 'inpatientsNoV' =>  $inpatientsVitals ? $inpatientsVitals['visitsNoVitals'] : '', 'outpatientsNoV' => $outpatientsVitals ? $outpatientsVitals['visitsNoVitals'] : ''];

            return response()->json(['shiftPerformance' => $shiftPerformance, 'details' => $details ? $details : '']);
        }, 2);
    }

    public function injectablesChartRate($shiftPerformance)
    {
        $shiftEnd = new Carbon($shiftPerformance->shift_end);
        $shiftEndTimer = $shiftEnd->subMinutes(20);
        $notCharted = [];

        $totalInjectablePrescriptions = $this->prescription
                                    // ->where('chartable', true)
                                    ->whereRelation('resource', 'sub_category', '=' ,'Injectable')
                                    ->where('discontinued', false)
                                    ->where('held', null)
                                    // ->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
                                    ->whereBetween('hms_bill_date', [$shiftPerformance->shift_start, $shiftEndTimer])
                                    ->count();

        $totalInjectablePrescriptionsCharted      = $this->prescription->prescriptionsChartedPerShift($shiftPerformance, 'medicationCharts');
        $totalInjectablePrescriptionsNotCharted   = $this->prescription->prescriptionsNotChartedPerShift($shiftPerformance, 'medicationCharts');

        foreach($totalInjectablePrescriptionsNotCharted as $NotChartedPrescription){
            array_push($notCharted, $NotChartedPrescription->visit->patient->card_no . ' ' . $NotChartedPrescription->visit->patient->first_name);
        }

        $notChartedUniqueInjectables = array_values(array_unique($notCharted));

        $all = new Collection(['totalInjectablePrescriptions' => $totalInjectablePrescriptions, 'totalInjectablePrescriptionsCharted' => $totalInjectablePrescriptionsCharted, 'notChartedUniqueInjectables' => $notChartedUniqueInjectables]);

        return $totalInjectablePrescriptions ? $all : null;
    }
    
    public function othersChartRate($shiftPerformance)
    {
        $shiftEnd = new Carbon($shiftPerformance->shift_end);
        $shiftEndTimer = $shiftEnd->subMinutes(20);
        $notCharted = [];

        $totalOtherPrescriptions = $this->prescription
                                    ->where('chartable', true)
                                    ->whereRelation('resource', 'sub_category', '!=' ,'Injectable')
                                    ->where('discontinued', false)
                                    ->where('held', null)
                                    ->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
                                    // ->whereBetween('hms_bill_date', [$shiftPerformance->shift_start, $shiftEndTimer])
                                    ->count();

        $totalOtherPrescriptionsCharted      = $this->prescription->prescriptionsChartedPerShift($shiftPerformance, 'nursingCharts', '!=');
        $totalOtherPrescriptionsNotCharted   = $this->prescription->prescriptionsNotChartedPerShift($shiftPerformance, 'nursingCharts', '!=');

        foreach($totalOtherPrescriptionsNotCharted as $NotChartedPrescription){
            array_push($notCharted, $NotChartedPrescription->visit->patient->card_no . ' ' . $NotChartedPrescription->visit->patient->first_name);
        }

        $notChartedUniqueOthers = array_values(array_unique($notCharted));

        $all = new Collection(['totalOtherPrescriptions' => $totalOtherPrescriptions, 'totalOtherPrescriptionsCharted' => $totalOtherPrescriptionsCharted, 'notChartedUniqueOthers' => $notChartedUniqueOthers]);

        return $totalOtherPrescriptions ? $all : null;
    }

    public function injectablesGivenRate($shiftPerformance)
    {
        $shiftEnd = new Carbon($shiftPerformance->shift_end);
        $shiftEndTimer = $shiftEnd->subMinutes(20);
        $notGiven = [];

        $totalInjectablesPrescriptions      = $this->prescription
                                            ->where('chartable', true)
                                            ->whereRelation('resource', 'sub_category', '=' ,'Injectable')
                                            ->where('discontinued', false)
                                            ->where('held', null)
                                            ->whereHas('medicationCharts')
                                            // ->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
                                            ->whereBetween('hms_bill_date', [$shiftPerformance->shift_start, $shiftEndTimer])
                                            ->count();

            $totalInjectablePrescriptionsStarted = $this->prescription->prescriptionsGivenPerShift($shiftPerformance, 'medicationCharts');
            $totalInjectablePrescriptionsNotStarted   = $this->prescription->prescriptionsNotGivenPerShift($shiftPerformance, 'medicationCharts');

            foreach($totalInjectablePrescriptionsNotStarted as $NotStartedPrescription){
                array_push($notGiven, $NotStartedPrescription->visit->patient->card_no . ' ' . $NotStartedPrescription->visit->patient->first_name);
            }
    
            $notStartedUniqueIjnectables = array_values(array_unique($notGiven));
    
            $all = new Collection(['totalInjectablePrescriptions' => $totalInjectablesPrescriptions, 'totalInjectablePrescriptionsStarted' => $totalInjectablePrescriptionsStarted, 'notStartedUniqueInjectables' => $notStartedUniqueIjnectables]);
    
            return $totalInjectablesPrescriptions ? $all : null;
    }

    public function othersDoneRate($shiftPerformance)
    {
        $shiftEnd = new Carbon($shiftPerformance->shift_end);
        $shiftEndTimer = $shiftEnd->subMinutes(20);
        $notGiven = [];

        $totalOtherPrescriptions         = $this->prescription
                                            ->where('chartable', true)
                                            ->whereRelation('resource', 'sub_category', '!=' ,'Injectable')
                                            ->where('discontinued', false)
                                            ->where('held', null)
                                            ->whereHas('nursingCharts')
                                            ->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
                                            // ->whereBetween('hms_bill_date', [$shiftPerformance->shift_start, $shiftEndTimer])
                                            ->count();

            $totalOtherPrescriptionsStarted      = $this->prescription->prescriptionsGivenPerShift($shiftPerformance, 'nursingCharts', '!=');
            $totalOtherPrescriptionsNotStarted   = $this->prescription->prescriptionsNotGivenPerShift($shiftPerformance, 'nursingCharts', '!=');

            foreach($totalOtherPrescriptionsNotStarted as $NotStartedPrescription){
                array_push($notGiven, $NotStartedPrescription->visit->patient->card_no . ' ' . $NotStartedPrescription->visit->patient->first_name);
            }
    
            $notStartedUniqueOthers = array_values(array_unique($notGiven));
    
            $all = new Collection(['totalOtherPrescriptions' => $totalOtherPrescriptions, 'totalOtherPrescriptionsStarted' => $totalOtherPrescriptionsStarted, 'notStartedUniqueOthers' => $notStartedUniqueOthers]);
    
            return $totalOtherPrescriptions ? $all : null;
    }

    public function firstMedicationResolution($shiftPerformance)
    {
        $shiftEnd = new Carbon($shiftPerformance->shift_end);
        $shiftEndTimer = $shiftEnd->subMinutes(20);

        $prescriptionsWithoutMc = $this->prescription
                                        // ->where('chartable', true)
                                        ->whereRelation('resource', 'sub_category', '=' ,'Injectable')
                                        ->where('discontinued', false)
                                        ->where('held', null)
                                        // ->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
                                        ->whereBetween('hms_bill_date', [$shiftPerformance->shift_start, $shiftEndTimer])
                                        ->whereDoesntHave('medicationCharts')->count();

        $prescriptionsWithMc    = $this->prescription
                                        // ->where('chartable', true)
                                        ->whereRelation('resource', 'sub_category', '=' ,'Injectable')
                                        ->where('discontinued', false)
                                        ->where('held', null)
                                        // ->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
                                        ->whereBetween('hms_bill_date', [$shiftPerformance->shift_start, $shiftEndTimer])
                                        ->whereHas('medicationCharts')->count();

        $averageFMRTime = DB::table('prescriptions')
                            ->selectRaw('AVG(TIME_TO_SEC(TIMEDIFF(medication_charts.time_given, prescriptions.created_at))) AS averageFMRTime')
                            ->leftJoin('medication_charts', 'prescriptions.id', 'medication_charts.prescription_id')
                            ->where('medication_charts.dose_count', 1)
                            ->where('prescriptions.held', null)
                            ->where('prescriptions.discontinued', false)
                            // ->whereBetween('prescriptions.created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
                            ->whereBetween('prescriptions.hms_bill_date', [$shiftPerformance->shift_start, $shiftEndTimer])
                            ->get()->first()->averageFMRTime;
        
        return $prescriptionsWithoutMc > 0 || $prescriptionsWithMc > 0 ? $averageFMRTime : null;     
    }

    public function firstServicesResolution($shiftPerformance)
    {
        $shiftEnd = new Carbon($shiftPerformance->shift_end);
        $shiftEndTimer = $shiftEnd->subMinutes(20);

        $prescriptionsWithoutNc = $this->prescription
                                        ->where('chartable', true)
                                        ->whereRelation('resource', 'sub_category', '!=' ,'Injectable')
                                        ->where('discontinued', false)
                                        ->where('held', null)
                                        ->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
                                        // ->whereBetween('hms_bill_date', [$shiftPerformance->shift_start, $shiftEndTimer])
                                        ->whereDoesntHave('nursingCharts')->count();

        $prescriptionsWithNc    = $this->prescription
                                        // ->where('chartable', true)
                                        ->whereRelation('resource', 'sub_category', '!=' ,'Injectable')
                                        ->where('discontinued', false)
                                        ->where('held', null)
                                        ->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
                                        // ->whereBetween('hms_bill_date', [$shiftPerformance->shift_start, $shiftEndTimer])
                                        ->whereHas('nursingCharts')->count();

        $averageFSRTime = DB::table('prescriptions')
                            ->selectRaw('AVG(TIME_TO_SEC(TIMEDIFF(nursing_charts.time_done, prescriptions.created_at))) AS averageFSRTime')
                            ->leftJoin('nursing_charts', 'prescriptions.id', 'nursing_charts.prescription_id')
                            ->where('nursing_charts.schedule_count', 1)
                            ->where('prescriptions.held', null)
                            ->where('prescriptions.discontinued', false)
                            ->whereBetween('prescriptions.created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
                            // ->whereBetween('prescriptions.hms_bill_date', [$shiftPerformance->shift_start, $shiftEndTimer])
                            ->get()->first()->averageFSRTime;
        
        return $prescriptionsWithoutNc > 0 || $prescriptionsWithNc > 0 ? $averageFSRTime : null;     
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

    public function serviceTime($shiftPerformance)
    {
        $servicesDueInShift = $this->nursingChart
                                ->whereBetween('scheduled_time', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])
                                ->orWhereBetween('time_done', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])
                                ->whereRelation('visit', 'admission_status', '!=', 'Outpatient')
                                ->count();

        $averageServiceTimes = DB::table('nursing_charts')
                                ->selectRaw('AVG(TIME_TO_SEC(TIMEDIFF(nursing_charts.time_done, nursing_charts.scheduled_time))) AS averageServiceTime')
                                ->leftJoin('visits', 'visits.id', 'nursing_charts.visit_id')
                                ->whereBetween('scheduled_time', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])
                                ->where('visits.admission_status', '!=', 'OutPatient')
                                ->get()->first()->averageServiceTime;

        $averageServiceTime = $servicesDueInShift ? ($averageServiceTimes ? $averageServiceTimes : null) : null;

        return $servicesDueInShift > 0 ? $averageServiceTime : null;        
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
                ->whereRelation('patient', 'patient_type', '!=', 'ANC')
                ->count();

        $visitsVCount = $this->visit
                ->where('created_at', '<', $shiftStartTimer)
                ->where(function (EloquentBuilder $query) {
                    $query->where('admission_status', '=', 'Inpatient')
                    ->orWhere('admission_status', '=', 'Observation');
                })
                ->where('doctor_done_by', '=', null)
                ->whereRelation('patient', 'patient_type', '!=', 'ANC')
                ->where(function (EloquentBuilder $query) use ($shiftPerformance, $count) {
                    $query->whereHas('vitalSigns', function ($query) use ($shiftPerformance) {
                                $query->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end]);
                        }, '>=', $count);
                        // ->orWhereHas('antenatalRegisteration.ancVitalSigns', function ($query) use ($shiftPerformance) {
                        //         $query->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end]);
                        // }, '>=', $count);
                })
                ->count();
               
        $visitsNoVitals = $this->visit
                ->where('created_at', '<', $shiftStartTimer)
                ->where(function (EloquentBuilder $query) {
                    $query->where('admission_status', '=', 'Inpatient')
                    ->orWhere('admission_status', '=', 'Observation');
                })
                ->where('doctor_done_by', '=', null)
                ->whereRelation('patient', 'patient_type', '!=', 'ANC')
                ->where(function (EloquentBuilder $query) use ($shiftPerformance, $count) {
                    $query->whereHas('vitalSigns', function ($query) use ($shiftPerformance) {
                            $query->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end]);
                        }, '<', $count);
                        // ->orWhereHas('antenatalRegisteration.ancVitalSigns', function ($query) use ($shiftPerformance) {
                        //     $query->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end]);
                        // }, '<', $count);
                })
                ->get();

        foreach($visitsNoVitals as $visitNoVitals){
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

        $convertInjectablesChartRate    =   $shiftPerformance->injectable_chart_rate === null ? null : 
                                            ($this->percentFromStringFraction($shiftPerformance->injectable_chart_rate) / 100) * 20 ; 
                                            $shiftPerformance->injectable_chart_rate === null ? '' : $totalPoints++;

        $convertOthersChartRate         =   $shiftPerformance->others_chart_rate === null ? null : 
                                            ($this->percentFromStringFraction($shiftPerformance->others_chart_rate) / 100) * 20 ; 
                                            $shiftPerformance->others_chart_rate === null ? '' : $totalPoints++;

        $convertInjectablesGivenRate    =   $shiftPerformance->injectables_given_rate === null ? null: 
                                            ($this->percentFromStringFraction($shiftPerformance->injectables_given_rate) / 100) * 20; 
                                            $shiftPerformance->injectables_given_rate === null ? '': $totalPoints++;

        $convertOthersGivenRate         =   $shiftPerformance->others_done_rate === null ? null: 
                                            ($this->percentFromStringFraction($shiftPerformance->others_done_rate) / 100) * 20; 
                                            $shiftPerformance->others_done_rate === null ? '': $totalPoints++;

        $convertFirstMedRes             =   $shiftPerformance->first_med_res === null ? null :
                                            ($this->secondsToPercent($shiftPerformance->first_med_res, 'FMR') /100 ) * 20; 
                                            $shiftPerformance->first_med_res === null ? '' : $totalPoints++;

        $convertFirstServRes            =   $shiftPerformance->first_serv_res === null ? null :
                                            ($this->secondsToPercent($shiftPerformance->first_serv_res, 'FMR') /100 ) * 20; 
                                            $shiftPerformance->first_serv_res === null ? '' : $totalPoints++;

        $convertFirstVitalsRes          =   $shiftPerformance->first_vitals_res === null ? null :
                                            ($this->secondsToPercent($shiftPerformance->first_vitals_res, 'FVR') / 100) * 20; 
                                            $shiftPerformance->first_vitals_res === null ? '' : $totalPoints++;

        $convertMedicationTime          =   $shiftPerformance->medication_time === null ? null : 
                                            ($this->secondsToPercent($shiftPerformance->medication_time, 'MT') / 100) * 20; 
                                            $shiftPerformance->medication_time === null ? '' : $totalPoints++;

        $convertServiceTime             =   $shiftPerformance->service_time === null ? null : 
                                            ($this->secondsToPercent($shiftPerformance->service_time, 'MT') / 100) * 20; 
                                            $shiftPerformance->service_time === null ? '' : $totalPoints++;

        $convertInPsVC                  =   $shiftPerformance->inpatient_vitals_count === null ? null : 
                                            ($this->percentFromStringFraction($shiftPerformance->inpatient_vitals_count) / 100) * 20; 
                                            $shiftPerformance->inpatient_vitals_count === null ? '' : $totalPoints++;

        $convertOutPsVC                 =   $shiftPerformance->outpatient_vitals_count === null ? null : 
                                            ($this->percentFromStringFraction($shiftPerformance->outpatient_vitals_count) / 100) * 20; 
                                            $shiftPerformance->outpatient_vitals_count === null ? '' : $totalPoints++;
        
        $preformance = $totalPoints ? ($convertInjectablesChartRate + $convertOthersChartRate + $convertInjectablesGivenRate + $convertOthersGivenRate + $convertFirstMedRes + $convertFirstServRes + $convertFirstVitalsRes + $convertMedicationTime + $convertServiceTime + $convertInPsVC + $convertOutPsVC)/($totalPoints*20) * 100 : 0;
            
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
                'injectablesChartRate'  => $shiftPerformance->injectables_chart_rate,
                'othersChartRate'       => $shiftPerformance->others_chart_rate,
                'injectablesGivenRate'  => $shiftPerformance->injectables_given_rate,
                'othersGivenRate'       => $shiftPerformance->others_done_rate,
                'firstMedRes'           => $shiftPerformance->first_med_res ? CarbonInterval::seconds($shiftPerformance->first_med_res)->cascade()->forHumans() : null,
                'firstServRes'          => $shiftPerformance->first_serv_res ? CarbonInterval::seconds($shiftPerformance->first_serv_res)->cascade()->forHumans() : null,
                'firstVitalsRes'        => $shiftPerformance->first_vitals_res ? CarbonInterval::seconds($shiftPerformance->first_vitals_res)->cascade()->forHumans() : null,
                'medicationTime'        => $shiftPerformance->medication_time ? ($shiftPerformance->medication_time < 0 ? 'Many served on time': CarbonInterval::seconds($shiftPerformance->medication_time)->cascade()->forHumans()) : null,
                'serviceTime'           => $shiftPerformance->service_time ? ($shiftPerformance->service_time < 0 ? 'Many done on time': CarbonInterval::seconds($shiftPerformance->service_time)->cascade()->forHumans()) : null,
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