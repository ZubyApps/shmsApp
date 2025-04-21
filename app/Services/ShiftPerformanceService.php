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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

Class ShiftPerformanceService
{
    public function __construct(
        private readonly ShiftPerformance $shiftPerformance,
        private readonly Prescription $prescription,
        private readonly MedicationChart $medicationChart,
        private readonly NursingChart $nursingChart,
        private readonly Visit $visit,
        private readonly HelperService $helperService
        )
    {
        
    }

    public function update()
    {
        return DB::transaction(function () {
            $shiftPerformance = $this->shiftPerformance->where('department', 'Nurse')->where('is_closed', false)->orderBy('id', 'desc')->first();
            
            if (!$shiftPerformance){
                return;
            }

            $nursesOnDuty = User::whereRelation('designation', 'designation', 'Nurse')->where('is_active', true)->pluck('username')->toArray();

            $shiftsPrescriptions        = $this->shiftsPrescriptions($shiftPerformance);
            $injectablePrescriptions    = $this->injectablePrescriptions($shiftsPrescriptions);
            $otherPrescriptions         = $this->otherPrescriptions($shiftsPrescriptions);
            $shiftsVisits               = $this->shiftsVisits($shiftPerformance);
            
            $injectablesChartRate   = $this->injectablesChartRate($injectablePrescriptions);
            $othersChartRate        = $this->othersChartRate($otherPrescriptions);
            $injectablesGivenRate   = $this->injectablesGivenRate($injectablePrescriptions);
            $othersDoneRate         = $this->othersDoneRate($otherPrescriptions);
            $inpatientsVitals       = $this->inpatientsVitalsignsCount($shiftPerformance);
            $outpatientsVitals      = $this->outpatientssVitalsignsCount($shiftsVisits);
            $medicationTimeValues   = $this->medicationTime($shiftPerformance);
            $serviceTimeValues      = $this->serviceTime($shiftPerformance);

            $shiftPerformance->update([
                    'injectables_chart_rate'    => $injectablesChartRate ? $injectablesChartRate['totalInjectablePrescriptionsCharted'] . '/' . $injectablesChartRate['totalInjectablePrescriptions'] : $injectablesChartRate,
                    'others_chart_rate'         => $othersChartRate ? $othersChartRate['totalOtherPrescriptionsCharted'] . '/' . $othersChartRate['totalOtherPrescriptions'] : $othersChartRate,
                    'injectables_given_rate'    => $injectablesGivenRate ? $injectablesGivenRate['totalInjectablePrescriptionsStarted'] . '/' . $injectablesGivenRate['totalInjectablePrescriptions'] : $injectablesGivenRate,
                    'others_done_rate'          => $othersDoneRate ? $othersDoneRate['totalOtherPrescriptionsStarted'] . '/' . $othersDoneRate['totalOtherPrescriptions'] : $othersDoneRate,
                    'first_med_res'             => $this->firstMedicationResolution($shiftPerformance, $injectablePrescriptions),
                    'first_serv_res'            => $this->firstServicesResolution($shiftPerformance, $otherPrescriptions),
                    'first_vitals_res'          => $this->firstVitalsignsResolution($shiftPerformance, $shiftsVisits),
                    'medication_time'           => $medicationTimeValues ? $medicationTimeValues['averageMedicationTime'] : $medicationTimeValues, //$this->medicationTime($shiftPerformance),
                    'service_time'              => $serviceTimeValues ? $serviceTimeValues['averageServiceTime'] : $serviceTimeValues, //$this->serviceTime($shiftPerformance),
                    'inpatient_vitals_count'    => $inpatientsVitals ? $inpatientsVitals['visitsVCount'] . '/' . $inpatientsVitals['visitsCount'] : $inpatientsVitals,
                    'outpatient_vitals_count'   => $outpatientsVitals ? $outpatientsVitals['visitsVCount'] . '/' . $outpatientsVitals['visitsCount'] : $outpatientsVitals,
                    'staff'                     => $nursesOnDuty
                ]);

                $busyCount = ($injectablesChartRate ? $injectablesChartRate['totalInjectablePrescriptions'] : 0) + ($medicationTimeValues ? $medicationTimeValues['medicationsDueInShift'] : 0) + ($serviceTimeValues ? $serviceTimeValues['servicesNotDone'] : 0);
                $medicationTimeCounts = ($medicationTimeValues ? $medicationTimeValues['medicationsNotGiven'] : 0) . ' medication(s)';
                $serviceTimeCounts = ($serviceTimeValues ? $serviceTimeValues['servicesNotDone'] : 0) . ' service(s)';

                $shiftPerformance->update([
                    'performance'  => $this->getPerformance($shiftPerformance, $busyCount),
                ]);
    
                $shiftPerformance->first_med_res    = $shiftPerformance->first_med_res ? CarbonInterval::seconds($shiftPerformance->first_med_res)->cascade()->forHumans() : null;
                $shiftPerformance->first_serv_res    = $shiftPerformance->first_serv_res ? CarbonInterval::seconds($shiftPerformance->first_serv_res)->cascade()->forHumans() : null;
                $shiftPerformance->first_vitals_res = $shiftPerformance->first_vitals_res ? CarbonInterval::seconds($shiftPerformance->first_vitals_res)->cascade()->forHumans() : null;
                $shiftPerformance->medication_time  = $shiftPerformance->medication_time ? ($shiftPerformance->medication_time < 0 ? 'Many served on time': CarbonInterval::seconds($shiftPerformance->medication_time)->cascade()->forHumans()) . ' (' . $medicationTimeCounts . ' left)' : 'Medications count ('. $medicationTimeCounts .')' ;
                $shiftPerformance->service_time  = $shiftPerformance->service_time ? ($shiftPerformance->service_time < 0 ? 'Many served on time': CarbonInterval::seconds($shiftPerformance->service_time)->cascade()->forHumans()) . ' (' . $serviceTimeCounts . ' left)' : 'Services count ('. $serviceTimeCounts .')';
                $details = [
                    'notChartedInjectables' => $injectablesChartRate ? $injectablesChartRate['notChartedUniqueInjectables'] : '',
                    'notChartedOthers' => $othersChartRate ? $othersChartRate['notChartedUniqueOthers'] : '',
                    'notStartedInjectables' => $injectablesGivenRate ? $injectablesGivenRate['notStartedUniqueInjectables'] : '',
                    'notStartedOthers' => $othersDoneRate ? $othersDoneRate['notStartedUniqueOthers'] : '',
                    'inpatientsNoV' => $inpatientsVitals ? $inpatientsVitals['visitsNoVitals'] : '',
                    'outpatientsNoV' => $outpatientsVitals ? $outpatientsVitals['visitsNoVitals'] : '',
                    'notGivenMedications' => $medicationTimeValues ? $medicationTimeValues['notGivenMedications'] : '',
                    'notDoneServices' => $serviceTimeValues ? $serviceTimeValues['notDoneServices'] : '',
                ];

            return response()->json(['shiftPerformance' => $shiftPerformance, 'details' => $details ? $details : '']);
        }, 2);
    }

    public function shiftsPrescriptions($shiftPerformance)
    {
        $timingSwaper           = $this->helperService->prescriptionTimeSwapper();
        $column                 = $this->setColumn($timingSwaper);
        $shiftEnd               = new Carbon($shiftPerformance->shift_end);
        $shiftEndTimer          = $shiftEnd->subMinutes(20);
        return $this->prescription
                    ->with(['visit.patient', 'medicationCharts', 'nursingCharts', 'resource'])
                    ->where('discontinued', false)
                    ->where('held', null)
                    ->whereBetween($column, [$shiftPerformance->shift_start, $shiftEndTimer])
                    ->get();
    }

    public function injectablePrescriptions(Collection $shiftPrescriptions)
    {
        return $shiftPrescriptions->where('resource.sub_category', '=', 'Injectable');
    }

    public function otherPrescriptions(Collection $shiftPrescriptions)
    {
        return $shiftPrescriptions->where('chartable', true)->where('resource.sub_category', '!=', 'Injectable');
    }

    public function shiftsVisits($shiftPerformance)
    {
        $shiftEnd = new Carbon($shiftPerformance->shift_end);
        $shiftEndTimer = $shiftEnd->subMinutes(10);

        return $this->visit
            ->with(['vitalSigns'])
            ->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
            ->where('closed', false)
            ->whereRelation('patient', 'patient_type', '!=', 'ANC')
            ->get();
    }

    public function setColumn($timingSwaper)
    {
        return $timingSwaper ? 'created_at' : 'hms_bill_date';
    }

    public function injectablesChartRate(?Collection $prescriptions)
    {
        $totalInjectablePrescriptions = $prescriptions->count();

        $totalInjectablePrescriptionsCharted = $prescriptions->filter(function ($prescription) {
            return $prescription->medicationCharts->isNotEmpty();
        })->count();

        $totalInjectablePrescriptionsNotCharted = $prescriptions->filter(function ($prescription) {
            return $prescription->medicationCharts->isEmpty();
        });

        $notChartedUniqueInjectables = $totalInjectablePrescriptionsNotCharted->map(function ($prescription) {
            return $prescription->visit->patient->card_no . ' ' . $prescription->visit->patient->first_name;
        })->unique()->values()->all();

        $all = new Collection([
            'totalInjectablePrescriptions' => $totalInjectablePrescriptions,
            'totalInjectablePrescriptionsCharted' => $totalInjectablePrescriptionsCharted,
            'notChartedUniqueInjectables' => array_values($notChartedUniqueInjectables)
        ]);

        return $totalInjectablePrescriptions ? $all : null;
    }

    public function othersChartRate(Collection $prescriptions)
    {
        $totalOtherPrescriptions = $prescriptions->count();

        $totalOtherPrescriptionsCharted = $prescriptions->filter(function ($prescription) {
            return $prescription->nursingCharts->isNotEmpty();
        })->count();

        $totalOtherPrescriptionsNotCharted = $prescriptions->filter(function ($prescription) {
            return $prescription->nursingCharts->isEmpty();
        });

        $notChartedUniqueOthers = $totalOtherPrescriptionsNotCharted->map(function ($prescription) {
            return $prescription->visit->patient->card_no . ' ' . $prescription->visit->patient->first_name;
        })->unique()->values()->all();

        $all = new Collection([
            'totalOtherPrescriptions' => $totalOtherPrescriptions,
            'totalOtherPrescriptionsCharted' => $totalOtherPrescriptionsCharted,
            'notChartedUniqueOthers' => array_values($notChartedUniqueOthers)
        ]);

        return $totalOtherPrescriptions ? $all : null;
    }

    public function injectablesGivenRate(Collection $shiftsPrescriptions)
    {
        $prescriptions = $shiftsPrescriptions->filter(function ($prescription) {
            return $prescription->medicationCharts->isNotEmpty();
        });

        $totalInjectablesPrescriptions = $prescriptions->count();

        $totalInjectablePrescriptionsStarted = $prescriptions->filter(function ($prescription) {
            return $prescription->medicationCharts->filter(function ($chart) {
                return $chart->time_given !== null;
            })->isNotEmpty();
        })->count();

        $totalInjectablePrescriptionsNotStarted = $prescriptions->filter(function ($prescription) {
            return $prescription->medicationCharts->first()->time_given === null;
        });

        $notStartedUniqueInjectables = $totalInjectablePrescriptionsNotStarted->map(function ($prescription) {
            return $prescription->visit->patient->card_no . ' ' . $prescription->visit->patient->first_name;
        })->unique()->values()->all();

        $all = new Collection([
            'totalInjectablePrescriptions' => $totalInjectablesPrescriptions,
            'totalInjectablePrescriptionsStarted' => $totalInjectablePrescriptionsStarted,
            'notStartedUniqueInjectables' => array_values($notStartedUniqueInjectables)
        ]);

        return $totalInjectablesPrescriptions ? $all : null;
    }

    public function othersDoneRate(Collection $shiftsPrescriptions)
    {
        $prescriptions = $shiftsPrescriptions->filter(function ($prescription) {
            return $prescription->nursingCharts->isNotEmpty();
        });       

        $totalOtherPrescriptions = $prescriptions->count();

        $totalOtherPrescriptionsStarted = $prescriptions->filter(function ($prescription) {
            return $prescription->nursingCharts->filter(function ($chart) {
                return $chart->time_done !== null;
            })->isNotEmpty();
        })->count();

        $totalOtherPrescriptionsNotStarted = $prescriptions->filter(function ($prescription) {
            return $prescription->nursingCharts->first()->time_done === null;
        });

        $notStartedUniqueOthers = $totalOtherPrescriptionsNotStarted->map(function ($prescription) {
            return $prescription->visit->patient->card_no . ' ' . $prescription->visit->patient->first_name;
        })->unique()->values()->all();

        $all = new Collection([
            'totalOtherPrescriptions' => $totalOtherPrescriptions,
            'totalOtherPrescriptionsStarted' => $totalOtherPrescriptionsStarted,
            'notStartedUniqueOthers' => array_values($notStartedUniqueOthers)
        ]);

        return $totalOtherPrescriptions ? $all : null;
    }

    public function firstMedicationResolution($shiftPerformance, $shiftsPrescriptions)
    {
        $prescriptionsWithoutMc = $shiftsPrescriptions->filter(function ($prescription) {
            return $prescription->medicationCharts->isEmpty();
        })->count();

        $prescriptionsWithMc = $shiftsPrescriptions->filter(function ($prescription) {
            return $prescription->medicationCharts->isNotEmpty();
        })->count();

        $shiftEnd               = new Carbon($shiftPerformance->shift_end);
        $shiftEndTimer          = $shiftEnd->subMinutes(20);
        $averageFMRTime = DB::table('prescriptions')
            ->selectRaw('AVG(TIME_TO_SEC(TIMEDIFF(medication_charts.time_given, prescriptions.created_at))) AS averageFMRTime')
            ->leftJoin('medication_charts', 'prescriptions.id', 'medication_charts.prescription_id')
            ->where('medication_charts.dose_count', 1)
            ->where('prescriptions.held', null)
            ->where('prescriptions.discontinued', false)
            ->whereBetween('prescriptions.hms_bill_date', [$shiftPerformance->shift_start, $shiftEndTimer])
            ->value('averageFMRTime');

        return $prescriptionsWithoutMc > 0 || $prescriptionsWithMc > 0 ? $averageFMRTime : null;
    }

    public function firstServicesResolution($shiftPerformance, $shiftsPrescriptions)
    {
        $prescriptionsWithoutNc = $shiftsPrescriptions->filter(function ($prescription) {
            return $prescription->nursingCharts->isEmpty();
        })->count();

        $prescriptionsWithNc = $shiftsPrescriptions->filter(function ($prescription) {
            return $prescription->nursingCharts->isNotEmpty();
        })->count();

        $shiftEnd = new Carbon($shiftPerformance->shift_end);
        $shiftEndTimer = $shiftEnd->subMinutes(20);
        $averageFSRTime = DB::table('prescriptions')
            ->selectRaw('AVG(TIME_TO_SEC(TIMEDIFF(nursing_charts.time_done, prescriptions.created_at))) AS averageFSRTime')
            ->leftJoin('nursing_charts', 'prescriptions.id', 'nursing_charts.prescription_id')
            ->where('nursing_charts.schedule_count', 1)
            ->where('prescriptions.held', null)
            ->where('prescriptions.discontinued', false)
            ->whereBetween('prescriptions.created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
            ->value('averageFSRTime');

        return $prescriptionsWithoutNc > 0 || $prescriptionsWithNc > 0 ? $averageFSRTime : null;
    }

    public function firstVitalsignsResolution($shiftPerformance, $shiftVisits)
    {
        // Use eager loading to reduce the number of queries
        // $visits = $this->visit
        //     ->with(['vitalSigns'])
        //     ->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
        //     ->get();

        $visitsWithoutVs = $shiftVisits->filter(function ($visit) {
            return $visit->vitalSigns->isEmpty();
        })->count();

        $visitsWithVs = $shiftVisits->filter(function ($visit) {
            return $visit->vitalSigns->isNotEmpty();
        })->count();

        $shiftEnd = new Carbon($shiftPerformance->shift_end);
        $shiftEndTimer = $shiftEnd->subMinutes(10);

        $averageFVRTime = DB::table('visits')
            ->selectRaw('AVG(TIME_TO_SEC(TIMEDIFF(first_vitalsigns, created_at))) AS averageFVRTime')
            ->whereBetween('visits.created_at', [$shiftPerformance->shift_start, $shiftEndTimer])
            ->value('averageFVRTime');

        return $visitsWithoutVs > 0 || $visitsWithVs > 0 ? $averageFVRTime : null;
    }

    public function medicationTime($shiftPerformance)
    {
        $medicationsDueInShift = $this->medicationChart
            ->whereBetween('scheduled_time', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])
            ->whereRelation('visit', 'admission_status', '!=', 'Outpatient')
            ->whereRelation('prescription', 'discontinued', false)
            ->whereRelation('visit', 'discharge_reason', null)
            ->get();

        $medicationsDueInShiftC     = $medicationsDueInShift->count();
        $medicationsGivenInShift    = $medicationsDueInShift->whereNotNull('time_given')->where('status', true)->count();
        $medicationsNotGiven        = $medicationsDueInShift->whereNull('time_given')->where('status', false);
        $medicationsNotGivenCount   = $medicationsDueInShift->whereNull('time_given')->where('status', false)->count();

        $notGivenMedications = $medicationsNotGiven->map(function ($medicationChart) {
            return $medicationChart->visit->patient->card_no . ' ' . $medicationChart->visit->patient->first_name;
        })->unique()->values()->all();

        $averageMedicationTime = DB::table('medication_charts')
            ->selectRaw('AVG(TIME_TO_SEC(TIMEDIFF(medication_charts.time_given, medication_charts.scheduled_time))) AS averageMedicationTime')
            ->leftJoin('visits', 'visits.id', 'medication_charts.visit_id')
            ->leftJoin('prescriptions', 'prescriptions.id', 'medication_charts.visit_id')
            ->whereBetween('medication_charts.scheduled_time', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])
            ->where('visits.admission_status', '!=', 'OutPatient')
            ->where('visits.discharge_reason', '=', null)
            ->where('prescriptions.discontinued', '=', false)
            ->value('averageMedicationTime');

            $all = new Collection([
                'medicationsDueInShift'     => $medicationsDueInShiftC,
                'medicationsGivenInShift'   => $medicationsGivenInShift,
                'averageMedicationTime'     => $averageMedicationTime,
                'notGivenMedications'       => array_values($notGivenMedications),
                'medicationsNotGiven'       => $medicationsNotGivenCount,
            ]);

        return $medicationsDueInShiftC > 0 ? $all : null;
    }

    public function serviceTime($shiftPerformance)
    {
        $servicesDueInShift = $this->nursingChart
                ->whereBetween('scheduled_time', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])
                ->whereRelation('visit', 'admission_status', '!=', 'Outpatient')
                ->whereRelation('prescription', 'discontinued', false)
                ->whereRelation('visit', 'discharge_reason', null)
                ->get();

        $servicesDueInShiftC    = $servicesDueInShift->count();
        $servicesDoneInShift    = $servicesDueInShift->whereNotNull('time_done')->where('status', true)->count();
        $servicesNotDone        = $servicesDueInShift->whereNull('time_done')->where('status', false);
        $servicesNotDoneCount   = $servicesDueInShift->whereNull('time_done')->where('status', false)->count();

        $notDoneServices = $servicesNotDone->map(function ($medicationChart) {
            return $medicationChart->visit->patient->card_no . ' ' . $medicationChart->visit->patient->first_name;
        })->unique()->values()->all();

        $averageServiceTime = DB::table('nursing_charts')
            ->selectRaw('AVG(TIME_TO_SEC(TIMEDIFF(nursing_charts.time_done, nursing_charts.scheduled_time))) AS averageServiceTime')
            ->leftJoin('visits', 'visits.id', 'nursing_charts.visit_id')
            ->leftJoin('prescriptions', 'prescriptions.id', 'nursing_charts.visit_id')
            ->whereBetween('nursing_charts.scheduled_time', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])
            ->where('visits.admission_status', '!=', 'OutPatient')
            ->where('visits.discharge_reason', '=', null)
            ->where('prescriptions.discontinued', '=', false)
            ->value('averageServiceTime');

            $all = new Collection([
                'servicesDueInShift'    => $servicesDueInShiftC,
                'servicesDoneInShift'   => $servicesDoneInShift,
                'averageServiceTime'    => $averageServiceTime,
                'notDoneServices'       => array_values($notDoneServices),
                'servicesNotDone'       => $servicesNotDoneCount,
            ]);

        return $servicesDueInShiftC > 0 ? $all : null;
    }

    public function inpatientsVitalsignsCount($shiftPerformance)
    {
        $shiftStart = new CarbonImmutable($shiftPerformance->shift_start);
        $shiftStartTimer = $shiftStart->addHour();
        $count = 2;

        if ($shiftPerformance->shift == 'Night Shift') {
            $shiftStartTimer = $shiftStart->addHours(3);
            $count = 3;
        }

        // Use eager loading to reduce the number of queries
        $visits = $this->visit
            ->with(['patient', 'vitalSigns'])
            ->whereNotNull('consulted')
            ->where('closed', false)
            ->where('created_at', '<', $shiftStartTimer)
            ->where(function (EloquentBuilder $query) {
                $query->where('admission_status', '=', 'Inpatient')
                    ->orWhere('admission_status', '=', 'Observation');
            })
            ->where('doctor_done_by', null)
            ->whereRelation('patient', 'patient_type', '!=', 'ANC')
            ->get();

        $visitsCount = $visits->count();

        $visitsVCount = $visits->filter(function ($visit) use ($shiftPerformance, $count) {
            return $visit->vitalSigns->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])->count() >= $count;
        })->count();

        $visitsNoVitals = $visits->filter(function ($visit) use ($shiftPerformance, $count) {
            return $visit->vitalSigns->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])->count() < $count;
        });

        $noVitals = $visitsNoVitals->map(function ($visit) use ($shiftPerformance, $count) {
            return $visit->patient->card_no . ' ' . $visit->patient->first_name . ' (' . $visit->vitalSigns->whereBetween('created_at', [$shiftPerformance->shift_start, $shiftPerformance->shift_end])->count() .'/'. $count . ')';
        })->all();

        $all = new Collection([
            'visitsCount' => $visitsCount,
            'visitsVCount' => $visitsVCount,
            'visitsNoVitals' => array_values($noVitals)
        ]);

        return $visitsCount ? $all : null;
    }

    public function outpatientssVitalsignsCount($visits)
    {
        $visitsCount = $visits->count();

        $visitsVCount = $visits->filter(function ($visit) {
            return $visit->vitalSigns->count() > 0;
        })->count();

        $visitsNoVitals = $visits->filter(function ($visit) {
            return $visit->vitalSigns->isEmpty();
        });

        $noVitals = $visitsNoVitals->map(function ($visit) {
            return $visit->patient->card_no . ' ' . $visit->patient->first_name. ' ' . ($visit->consulted ? '(Consulted)' : '(Waitinglist)');
        })->all();

        $all = new Collection([
            'visitsCount' => $visitsCount,
            'visitsVCount' => $visitsVCount,
            'visitsNoVitals' => array_values($noVitals)
        ]);

        return $visitsCount ? $all : null;
    }

    public function secondsToPercent($seconds, $indicator, $busyCount)
    {
        $benchMark  = (int)Cache::get('nursingBenchmark',100);
        $busyCount1 = $busyCount <= $benchMark;
        $busyCount2 = $busyCount > $benchMark && $busyCount < $benchMark + 10;
        $busyCount3 = $busyCount > $benchMark + 5 && $busyCount < $benchMark + 10;
        $busyCount4 = $busyCount > $benchMark + 10 && $busyCount < $benchMark + 15;
        $busyCount5 = $busyCount > $benchMark + 15 && $busyCount < $benchMark + 20;
        $busyCount6 = $busyCount > $benchMark + 20 && $busyCount < $benchMark + 25;
        $busyCount7 = $busyCount > $benchMark + 25 && $busyCount < $benchMark + 30;
        $busyCount7 = $busyCount > $benchMark + 30 && $busyCount < $benchMark + 35;
        $busyCount8 = $busyCount >= $benchMark + 35;

        $FMR    = $busyCount1 ? 0 : ($busyCount2 ? 300 : ($busyCount3 ? 480 :  ($busyCount4 ? 660 :  ($busyCount5 ? 840 :  ($busyCount6 ? 1020 :  ($busyCount7 ? 1200 :  ($busyCount8 ? 1380 :  1560)))))));
        $FVR    = $busyCount1 ? 0 : ($busyCount2 ? 300 : ($busyCount3 ? 480 :  ($busyCount4 ? 660 :  ($busyCount5 ? 840 :  ($busyCount6 ? 1020 :  ($busyCount7 ? 1200 :  ($busyCount8 ? 1380 :  1560)))))));
        $MT     = $busyCount1 ? 0 : ($busyCount2 ? 180 : ($busyCount3 ? 360 :  ($busyCount4 ? 540 :  ($busyCount5 ? 720 :  ($busyCount5 ? 900 :  ($busyCount5 ? 1080 :  ($busyCount5 ? 1260 :  1440)))))));

        $FMR1 = 600  + $FMR;
        $FMR2 = 900  + $FMR;
        $FMR3 = 1200 + $FMR;
        $FMR4 = 1500 + $FMR;
        $FMR5 = 1800 + $FMR;

        $FVR1 = 300  + $FVR;
        $FVR2 = 600  + $FVR;
        $FVR3 = 900  + $FVR;
        $FVR4 = 1200 + $FVR;
        $FVR5 = 1500 + $FVR;

        $MT1 = 180  + $MT;
        $MT2 = 360  + $MT;
        $MT3 = 660  + $MT;
        $MT4 = 960  + $MT;
        $MT5 = 1260 + $MT;

        if ($indicator == 'FMR'){
            return $seconds < $FMR1 ? 100 : ($seconds > $FMR1  && $seconds < $FMR2 ? 90 : ($seconds > $FMR2 && $seconds < $FMR3 ? 80 : ($seconds > $FMR3 && $seconds < $FMR4 ? 60 : ($seconds > $FMR4 && $seconds < $FMR5 ? 40 : 20))));
        }
        if ($indicator == 'FVR'){
            return $seconds < $FVR1 ? 100 : ($seconds >  $FVR1 && $seconds <  $FVR2 ? 90 : ($seconds >  $FVR2 && $seconds <  $FVR3 ? 80 : ($seconds >  $FVR3 && $seconds <  $FVR4 ? 70 : ($seconds >  $FVR4 && $seconds <  $FVR5 ? 60 : 40))));
        }
        if ($indicator == 'MT'){
            return $seconds < $MT1 ? 100 : ($seconds > $MT1 && $seconds < $MT2 ? 80 : ($seconds > $MT2 && $seconds < $MT3 ? 70 : ($seconds > $MT3 && $seconds < $MT4 ? 50 : ($seconds > $MT4 && $seconds < $MT5 ? 40 : 20))));
        }
    }

    public function getPerformance($shiftPerformance, $busyCount)
    {
        $totalPoints = 0;

        $convertInjectablesChartRate    =   $shiftPerformance->injectables_chart_rate === null ? null : 
                                            ($this->percentFromStringFraction($shiftPerformance->injectables_chart_rate) / 100) * 20 ; 
                                            $shiftPerformance->injectables_chart_rate === null ? '' : $totalPoints++;

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
                                            ($this->secondsToPercent($shiftPerformance->first_med_res, 'FMR', $busyCount) /100 ) * 20; 
                                            $shiftPerformance->first_med_res === null ? '' : $totalPoints++;

        $convertFirstServRes            =   $shiftPerformance->first_serv_res === null ? null :
                                            ($this->secondsToPercent($shiftPerformance->first_serv_res, 'FMR', $busyCount) /100 ) * 20; 
                                            $shiftPerformance->first_serv_res === null ? '' : $totalPoints++;

        $convertFirstVitalsRes          =   $shiftPerformance->first_vitals_res === null ? null :
                                            ($this->secondsToPercent($shiftPerformance->first_vitals_res, 'FVR', $busyCount) / 100) * 20; 
                                            $shiftPerformance->first_vitals_res === null ? '' : $totalPoints++;

        $convertMedicationTime          =   $shiftPerformance->medication_time === null ? null : 
                                            ($this->secondsToPercent($shiftPerformance->medication_time, 'MT', $busyCount) / 100) * 20; 
                                            $shiftPerformance->medication_time === null ? '' : $totalPoints++;

        $convertServiceTime             =   $shiftPerformance->service_time === null ? null : 
                                            ($this->secondsToPercent($shiftPerformance->service_time, 'MT', $busyCount) / 100) * 20; 
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