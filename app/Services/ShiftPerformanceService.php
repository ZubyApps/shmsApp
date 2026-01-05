<?php

declare(strict_types=1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use Illuminate\Support\Facades\{Cache, DB};
use Illuminate\Database\Eloquent\Collection;
use Carbon\{Carbon, CarbonImmutable, CarbonInterval};
use App\Models\{MedicationChart, NursingChart, Prescription, ShiftPerformance, User, Visit};
use Illuminate\Http\Request;

class ShiftPerformanceService
{
    public function __construct(
        private readonly ShiftPerformance $shiftPerformance,
        private readonly Prescription $prescription,
        private readonly MedicationChart $medicationChart,
        private readonly NursingChart $nursingChart,
        private readonly Visit $visit,
    ) {}

    /**
     * The main entry point to update performance metrics.
     */
    public function update()
    {
        return DB::transaction(function () {
            // 1. Identify the Active Shift
            $shift = $this->shiftPerformance->where('department', 'Nurse')
                ->where('is_closed', false)
                ->orderBy('id', 'desc')
                ->first();

            if (!$shift) return response()->json(['message' => 'No active shift'], 404);

            // 2. Fetch Base Data
            $nursesOnDuty = User::whereRelation('designation', 'designation', 'Nurse')
                ->where('is_active', true)->pluck('username')->toArray();

            $prescriptions = $this->getShiftsPrescriptions($shift);
            $visits        = $this->getShiftsVisits($shift);

            // 3. Calculate Rate Metrics (Raw Numbers)
            $injChart = $this->calculateRate($prescriptions->where('resource.sub_category', 'Injectable'), 'medicationCharts');
            $othChart = $this->calculateRate($prescriptions->where('chartable', true)->where('resource.sub_category', '!=', 'Injectable'), 'nursingCharts');
            
            // Only calculate "Given/Done" rates for items that have actually been charted
            $injGive  = $this->calculateRate($prescriptions->where('resource.sub_category', 'Injectable')->filter(fn($p) => $p->medicationCharts->isNotEmpty()), 'medicationCharts', 'time_given');
            $othDone  = $this->calculateRate($prescriptions->where('chartable', true)->where('resource.sub_category', '!=', 'Injectable')->filter(fn($p) => $p->nursingCharts->isNotEmpty()), 'nursingCharts', 'time_done');

            $inVs     = $this->getInpatientVitals($shift);
            $outVs    = $this->getOutpatientVitals($visits);
            
            $medTimes = $this->getMedicationTiming($shift);
            $serTimes = $this->getServiceTiming($shift);

            // 4. Resolve Resolution Averages (Seconds)
            $fmr = $this->firstMedicationResolution($shift, $prescriptions);
            $fsr = $this->firstServicesResolution($shift, $prescriptions);
            $fvr = $this->firstVitalsignsResolution($shift, $visits);

            // 5. Build Data Array for DB (Numeric/Clean)
            $updateData = [
                'injectables_chart_rate'  => $injChart ? "{$injChart['count']}/{$injChart['total']}" : null,
                'others_chart_rate'       => $othChart ? "{$othChart['count']}/{$othChart['total']}" : null,
                'injectables_given_rate'  => $injGive  ? "{$injGive['count']}/{$injGive['total']}" : null,
                'others_done_rate'        => $othDone  ? "{$othDone['count']}/{$othDone['total']}" : null,
                'first_med_res'           => $fmr,
                'first_serv_res'          => $fsr,
                'first_vitals_res'        => $fvr,
                'medication_time'         => $medTimes['avg'] ?? null,
                'service_time'            => $serTimes['avg'] ?? null,
                'inpatient_vitals_count'  => $inVs ? "{$inVs['count']}/{$inVs['total']}" : null,
                'outpatient_vitals_count' => $outVs ? "{$outVs['count']}/{$outVs['total']}" : null,
                'staff'                   => $nursesOnDuty,
            ];

            // 6. Calculate Final Score & Persist
            $busyCount = ($injChart['total'] ?? 0) + ($medTimes['due'] ?? 0) + ($serTimes['due'] ?? 0);
            $updateData['performance'] = $this->calculateOverallScore($updateData, $busyCount);

            $shift->update($updateData);

            // 7. Response Preparation
            return $this->prepareResponse($shift, $medTimes, $serTimes, compact('injChart', 'othChart', 'injGive', 'othDone', 'inVs', 'outVs'));
        }, 2);
    }

    /* ------------------- DATA FETCHING ------------------- */

    private function getShiftsPrescriptions($shift)
    {
        $shiftEndTimer = (new Carbon($shift->shift_end))->subMinutes(20);

        return $this->prescription->with([
            'visit' => function($query){
                $query->select('id', 'patient_id')
                    ->with(['patient:id,first_name,card_no']);
            }, 
            'medicationCharts:id,prescription_id,time_given', 
            'nursingCharts:id,prescription_id,time_done', 
            'resource:id,sub_category'
            ])
            ->whereRelation('visit', 'doctor_done_by')
            ->where('discontinued', false)
            ->whereNull('held')
            ->whereBetween('created_at', [$shift->shift_start, $shiftEndTimer])
            ->whereBetween('hms_bill_date', [$shift->shift_start, $shiftEndTimer])
            ->get();
    }

    private function getShiftsVisits($shift)
    {
        $shiftEndTimer = (new Carbon($shift->shift_end))->subMinutes(10);

        return $this->visit->select('id', 'consulted', 'patient_id')
            ->with(['vitalSigns:id,visit_id', 'patient:id,first_name,card_no'])
            ->whereBetween('created_at', [$shift->shift_start, $shiftEndTimer])
            ->where('closed', false)
            ->whereNull('doctor_done_by')
            ->where('visit_type', '!=', 'ANC')
            ->get();
    }

    /* ------------------- METRIC CALCULATORS ------------------- */

    private function calculateRate(Collection $items, string $relation, ?string $timeField = null): ?array
    {
        $total = $items->count();
        if ($total === 0) return null;

        $completed = $items->filter(function ($p) use ($relation, $timeField) {
            if (!$timeField) return $p->$relation->isNotEmpty();
            return $p->$relation->contains(fn($chart) => !is_null($chart->$timeField));
        });

        $failed = $items->diff($completed)->map(function ($p) {
            return "{$p->visit->patient->card_no} {$p->visit->patient->first_name}";
        })->unique()->values()->all();

        return ['total' => $total, 'count' => $completed->count(), 'failed_list' => $failed];
    }

    private function getMedicationTiming($shift): ?array
    {
        $query = $this->medicationChart->whereBetween('scheduled_time', [$shift->shift_start, $shift->shift_end])
            ->whereRelation('visit', fn($q) => $q->where('admission_status', '!=', 'Outpatient')->whereNull('discharge_reason'))
            ->whereRelation('prescription', 'discontinued', false);

        if (($due = (clone $query)->count()) === 0) return null;

        $notGiven = (clone $query)->select('id', 'visit_id', 'status', 'time_given', 'scheduled_time')->whereNull('time_given')->where('status', false)
            ->with(['visit' => function($query){
                $query->select('id', 'patient_id')
                    ->with(['patient:id,first_name,card_no']);
            }])->get()
            ->map(fn($m) => "{$m->visit->patient->card_no} {$m->visit->patient->first_name}")
            ->unique()->values()->all();

        return [
            'due'  => $due,
            'avg'  => (clone $query)->whereNotNull('time_given')->where('status', true)->avg(DB::raw('TIME_TO_SEC(TIMEDIFF(time_given, scheduled_time))')),
            'left' => count($notGiven),
            'list' => $notGiven
        ];
    }

    private function getServiceTiming($shift): ?array
    {
        $query = $this->nursingChart->whereBetween('scheduled_time', [$shift->shift_start, $shift->shift_end])
            ->whereRelation('visit', fn($q) => $q->where('admission_status', '!=', 'Outpatient')->whereNull('discharge_reason'))
            ->whereRelation('prescription', 'discontinued', false);

        if (($due = (clone $query)->count()) === 0) return null;

        $notDone = (clone $query)->select('id', 'status', 'visit_id', 'time_done', 'scheduled_time')->whereNull('time_done')->where('status', false)
            ->with(['visit' => function($query){
                $query->select('id', 'patient_id')
                    ->with(['patient:id,first_name,card_no']);
            }])->get()
            ->map(fn($c) => "{$c->visit->patient->card_no} {$c->visit->patient->first_name}")
            ->unique()->values()->all();

        return [
            'due'  => $due,
            'avg'  => (clone $query)->select('id', 'time_done', 'scheduled_time')->whereNotNull('time_done')->where('status', true)->avg(DB::raw('TIME_TO_SEC(TIMEDIFF(time_done, scheduled_time))')),
            'left' => count($notDone),
            'list' => $notDone
        ];
    }

    private function getInpatientVitals($shift): ?array
    {
        $startTimer = (new CarbonImmutable($shift->shift_start))->addHour();
        $target = 2;

        $visits = $this->visit->withCount(['vitalSigns as shift_vitals' => fn($q) => $q->whereBetween('created_at', [$shift->shift_start, $shift->shift_end])])
            ->with('patient:id,first_name,card_no')
            ->whereNotNull('consulted')
            ->where('closed', false)
            ->where('created_at', '<', $startTimer)
            ->whereIn('admission_status', ['Inpatient', 'Observation'])
            ->whereNull('doctor_done_by')
            ->get();

        if ($visits->isEmpty()) return null;

        $failed = $visits->where('shift_vitals', '<', $target)->map(fn($v) => "{$v->patient->card_no} {$v->patient->first_name} ({$v->shift_vitals}/$target)")->values()->all();

        return [
            'total' => $visits->count(),
            'count' => $visits->where('shift_vitals', '>=', $target)->count(),
            'failed_list' => $failed
        ];
    }

    private function getOutpatientVitals($visits): ?array
    {
        if ($visits->isEmpty()) return null;

        $completed = $visits->filter(fn($v) => $v->vitalSigns->isNotEmpty());
        
        $failed = $visits->diff($completed)->map(function ($v) {
            $status = $v->consulted ? '(Consulted)' : '(Waiting list)';
            return "{$v->patient->card_no} {$v->patient->first_name} $status";
        })->values()->all();

        return [
            'total' => $visits->count(),
            'count' => $completed->count(),
            'failed_list' => $failed
        ];
    }

    /* ------------------- RESOLUTION TIMES ------------------- */

    private function firstMedicationResolution($shift, $prescriptions)
    {
        if ($prescriptions->isEmpty()) return null;
        $timer = (new Carbon($shift->shift_end))->subMinutes(20);
        return DB::table('prescriptions')
            ->leftJoin('medication_charts', 'prescriptions.id', 'medication_charts.prescription_id')
            ->where('medication_charts.dose_count', 1)
            ->where('prescriptions.discontinued', false)
            ->whereBetween('prescriptions.hms_bill_date', [$shift->shift_start, $timer])
            ->avg(DB::raw('TIME_TO_SEC(TIMEDIFF(medication_charts.time_given, prescriptions.hms_bill_date))'));
    }

    private function firstServicesResolution($shift, $prescriptions)
    {
        if ($prescriptions->isEmpty()) return null;
        $timer = (new Carbon($shift->shift_end))->subMinutes(20);
        return DB::table('prescriptions')
            ->leftJoin('nursing_charts', 'prescriptions.id', 'nursing_charts.prescription_id')
            ->where('nursing_charts.schedule_count', 1)
            ->where('prescriptions.discontinued', false)
            ->whereBetween('prescriptions.created_at', [$shift->shift_start, $timer])
            ->avg(DB::raw('TIME_TO_SEC(TIMEDIFF(nursing_charts.time_done, prescriptions.created_at))'));
    }

    private function firstVitalsignsResolution($shift, $visits)
    {
        if ($visits->isEmpty()) return null;
        $timer = (new Carbon($shift->shift_end))->subMinutes(10);
        return DB::table('visits')->whereBetween('created_at', [$shift->shift_start, $timer])
            ->avg(DB::raw('TIME_TO_SEC(TIMEDIFF(first_vitalsigns, created_at))'));
    }

    /* ------------------- SCORING ENGINE ------------------- */

    private function calculateOverallScore(array $data, int $busyCount): float
    {
        $points = 0; $possible = 0;
        $indicatorMap = ['first_med_res' => 'FMR', 'first_serv_res' => 'FMR', 'first_vitals_res' => 'FVR', 'medication_time' => 'MT', 'service_time' => 'MT'];

        foreach ($indicatorMap as $key => $type) {
            if (!is_null($data[$key])) {
                $possible++;
                $points += ($this->secondsToPercent((float)$data[$key], $type, $busyCount) / 100) * 20;
            }
        }

        $rateFields = ['injectables_chart_rate', 'others_chart_rate', 'injectables_given_rate', 'others_done_rate', 'inpatient_vitals_count', 'outpatient_vitals_count'];
        foreach ($rateFields as $field) {
            if (!is_null($data[$field])) {
                $possible++;
                $points += ($this->percentFromFraction($data[$field]) / 100) * 20;
            }
        }

        return $possible ? round(($points / ($possible * 20)) * 100, 1) : 0;
    }

    private function secondsToPercent($seconds, $indicator, $busyCount): int
    {
        $benchmark = (int)Cache::get('nursingBenchmark', 100);
        $overload  = max(0, (int)floor(($busyCount - $benchmark) / 5));
        $buffer    = $overload * 180;

        return match ($indicator) {
            'FMR' => $this->scale($seconds, 600 + $buffer, 300),
            'FVR' => $this->scale($seconds, 300 + $buffer, 300),
            'MT'  => $this->scale($seconds, 180 + $buffer, 180),
            default => 0
        };
    }

    private function scale($val, $base, $step): int 
    {
        if ($val < $base) return 100;
        if ($val < $base + $step) return 90;
        if ($val < $base + ($step * 2)) return 80;
        if ($val < $base + ($step * 3)) return 60;
        if ($val < $base + ($step * 4)) return 40;
        return 20;
    }

    /* ------------------- PRESENTATION ------------------- */

    private function prepareResponse($shift, $medTimes, $serTimes, $meta)
    {
        $formatted = clone $shift;
        
        $toInterval = ['first_med_res', 'first_serv_res', 'first_vitals_res'];
        foreach ($toInterval as $f) {
            $formatted->$f = $shift->$f ? CarbonInterval::seconds((int)$shift->$f)->cascade()->forHumans() : null;
        }

        $mCount = ($medTimes['left'] ?? 0) . ' medication(s)';
        $sCount = ($serTimes['left'] ?? 0) . ' service(s)';

        $formatted->medication_time = $shift->medication_time 
            ? ($shift->medication_time < 0 ? 'Fast' : CarbonInterval::seconds((int)$shift->medication_time)->cascade()->forHumans()) . " ($mCount left)"
            : "Medications ($mCount left)";

        $formatted->service_time = $shift->service_time 
            ? ($shift->service_time < 0 ? 'Fast' : CarbonInterval::seconds((int)$shift->service_time)->cascade()->forHumans()) . " ($sCount left)"
            : "Services ($sCount left)";

        return response()->json([
            'shiftPerformance' => $formatted,
            'details' => [
                'notChartedInjectables' => $meta['injChart']['failed_list'] ?? [],
                'notChartedOthers'      => $meta['othChart']['failed_list'] ?? [],
                'notStartedInjectables' => $meta['injGive']['failed_list'] ?? [],
                'notStartedOthers'      => $meta['othDone']['failed_list'] ?? [],
                'inpatientsNoV'         => $meta['inVs']['failed_list'] ?? [],
                'outpatientsNoV'        => $meta['outVs']['failed_list'] ?? [],
                'notGivenMedications'   => $medTimes['list'] ?? [],
                'notDoneServices'       => $serTimes['list'] ?? [],
            ]
        ]);
    }

    private function percentFromFraction(string $fraction): float
    {
        $parts = explode('/', $fraction);
        return (isset($parts[1]) && $parts[1] > 0) ? ($parts[0] / $parts[1]) * 100 : 0;
    }

    public function getShiftPerformance(DataTableQueryParams $params, $data)
    {
        return $this->shiftPerformance
            ->where('department', $data->department)
            // Only apply the performance filter if a search term exists
            ->when($params->searchTerm, function ($query) use ($params) {
                return $query->where('performance', '>', $params->searchTerm);
            })
            // Only apply month/year filter if $data->date exists
            ->when($data->date, function ($query) use ($data) {
                $date = new CarbonImmutable($data->date);
                return $query->whereMonth('created_at', $date->month)
                            ->whereYear('created_at', $date->year);
            })
            // Only apply date range if start and end dates exist
            ->when($data->startDate && $data->endDate, function ($query) use ($data) {
                return $query->whereBetween('created_at', [
                    $data->startDate . ' 00:00:00', 
                    $data->endDate . ' 23:59:59'
                ]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(
                $params->length, 
                ['*'], 
                'page', 
                round(($params->start / $params->length) + 1)
            );
    }

        public function getNursesShiftPerformanceTransformer(): callable
    {
        return function (ShiftPerformance $shiftPerformance) {
            return [
                'id'                    => $shiftPerformance->id,
                'department'            => $shiftPerformance->department,
                'shift'                 => $shiftPerformance->shift,
                'start'                 => $shiftPerformance->shift_start?->format('d/M/y g:ia'),
                'end'                   => $shiftPerformance->shift_end?->format('d/M/y g:ia'),
                'injectablesChartRate'  => $shiftPerformance->injectables_chart_rate,
                'othersChartRate'       => $shiftPerformance->others_chart_rate,
                'injectablesGivenRate'  => $shiftPerformance->injectables_given_rate,
                'othersGivenRate'       => $shiftPerformance->others_done_rate,
                // Using the helper below to keep this clean
                'firstMedRes'           => $this->formatTime($shiftPerformance->first_med_res),
                'firstServRes'          => $this->formatTime($shiftPerformance->first_serv_res),
                'firstVitalsRes'        => $this->formatTime($shiftPerformance->first_vitals_res),
                'medicationTime'        => $this->formatTime($shiftPerformance->medication_time, 'Many served on time'),
                'serviceTime'           => $this->formatTime($shiftPerformance->service_time, 'Many done on time'),
                'intpatientVitalsCount' => $shiftPerformance->inpatient_vitals_count,
                'outpatientVitalsCount' => $shiftPerformance->outpatient_vitals_count,
                'performance'           => $shiftPerformance->performance . '%',
                'staff'                 => $shiftPerformance->staff, // Assuming this is cast to array in Model
                'closed'                => $shiftPerformance->is_closed,
            ];
        };
    }

    /**
     * Helper to keep the transformer readable
     */
    private function formatTime($seconds, string $negativeMsg = 'Fast'): ?string
    {
        if ($seconds === null) return null;
        if ($seconds < 0) return $negativeMsg;

        return CarbonInterval::seconds((int)$seconds)->cascade()->forHumans(['short' => true]);
    }

    public function updateStaff(Request $data, ShiftPerformance $shiftPerformance)
    {
        return $shiftPerformance->update([
            'staff' => $data->staff
        ]);
    }
}