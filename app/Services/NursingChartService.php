<?php

declare(strict_types = 1);

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Ward;
use Carbon\CarbonPeriod;
use Carbon\CarbonInterval;
use Carbon\CarbonImmutable;
use App\Models\NursingChart;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DataObjects\DataTableQueryParams;
use Illuminate\Database\Eloquent\Builder;

class NursingChartService
{
    public function __construct(
        private readonly NursingChart $nursingChart, 
        private readonly PrescriptionService $prescriptionService,
        private readonly Ward $ward,
        private readonly HelperService $helperService
        )
    {
    }

    // public function create(Request $data, User $user)
    // {
    //     $tz = 'Africa/Lagos';
    //     $hours    = strtolower($data->intervals) == 'hours';
    //     $interval = $hours ? CarbonInterval::minutes($data->frequency) : CarbonInterval::hours($data->frequency);
    //     $start    = (new CarbonImmutable($data->date, $tz));
    //     $end      = $hours ? $start->addHours($data->value) : $start->addDays($data->value);
    //     $dates    = new CarbonPeriod($start, $interval, $end, CarbonPeriod::EXCLUDE_END_DATE);

    //     if (count($dates) > 120) {
    //         return response()->json(
    //             ['errors' => [
    //                 'frequency' => ['This frequency may be too high'],
    //                 'intervals' => ['or the hours/days are too many']
    //         ]], 422);
    //     }

    //     $charts = [];
    //     $iteration = 0;
    //     foreach ($dates as $date) {
    //         $iteration++;
    //         $charts = $user->nursingCharts()->create([
    //             'prescription_id'   => $data->prescriptionId,
    //             'consultation_id'   => $data->conId,
    //             'visit_id'          => $data->visitId,
    //             'care_prescribed'   => $data->service,
    //             'scheduled_time'    => new Carbon($date, $tz),
    //             'schedule_count'    => $iteration
    //         ]);
    //     }
        
    //     if ($data->date){
    //         $date = (new CarbonImmutable($data->date, $tz));
    //         $date < Carbon::now($tz) ? $reason = 'Charted backward' : $reason = 'Charted Forward';
    //         $data->merge(['reason' => $reason]);
    //         $this->prescriptionService->hold($data, $charts->prescription, $user);
    //     }

    //     return $charts;
    // }

    public function create(Request $data, User $user)
    {
        $tz       = 'Africa/Lagos';
        $isHours  = strtolower($data->intervals) == 'hour(s)';
        $interval = $isHours ? CarbonInterval::minutes($data->frequency) : CarbonInterval::hours($data->frequency);
        
        $start = new CarbonImmutable($data->date, $tz);
        $end   = $isHours ? $start->addHours($data->value) : $start->addDays($data->value);
        $dates = new CarbonPeriod($start, $interval, $end, CarbonPeriod::EXCLUDE_END_DATE);

        if (count($dates) > 120) {
            return response()->json(['errors' => [
                'frequency' => ['This frequency may be too high'],
                'intervals' => ['or the hours/days are too many']
            ]], 422);
        }

        return DB::transaction(function () use ($data, $user, $dates, $tz) {
            $batch      = [];
            $iteration  = 0;
            $now        = now($tz); // For timestamps
            // 1. Prepare the data array (No DB hits here)
            foreach ($dates as $date) {
                $iteration++;
                $batch[] = [
                    'user_id'         => $user->id,
                    'prescription_id' => $data->prescriptionId,
                    'consultation_id' => $data->conId,
                    'visit_id'        => $data->visitId,
                    'scheduled_time'  => $date->toDateTimeString(),
                    'care_prescribed'   => $data->service,
                    'schedule_count'    => $iteration,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }

            // 2. Perform ONE Bulk Insert (Massively faster)
            if (empty($batch)) {
               throw new Exception("No dates generated for charting.");
            }

            $inserted = NursingChart::insert($batch);

            // 3. Handle Prescription Status
            if ( $inserted && $data->date) {
                $startDate = new CarbonImmutable($data->date, $tz);
                $reason    = $startDate->isPast() ? 'Charted backward' : 'Charted Forward';
                
                $data->merge(['reason' => $reason]);
                
                // Optimization: Use the ID directly to avoid fetching the Prescription model if not needed
                $prescription = Prescription::find($data->prescriptionId);
                $this->prescriptionService->hold($data, $prescription, $user);
            }

            // Return the last state or a success indicator
            return response()->json(['message' => 'Service charted successfully'], 201);
        });
    }

    public function updateRecord(Request $data, NursingChart $nursingChart, User $user)
    {
        $scheduledTime = New Carbon($nursingChart->scheduled_time);

        if($data->notDone == 'Snooze 30 mins'){
            return $nursingChart->update([
                'time_done'         => Carbon::now(),
                'not_done'          => $data->notDone,
                'done_by'           => $user->id,
                'note'              => $data->note,
                'scheduled_time'    => $scheduledTime->addMinutes(30),    
             ]);
        }

        if($data->notDone == 'Snooze 60 mins'){
            return $nursingChart->update([
                'time_done'         => Carbon::now(),
                'not_done'          => $data->notDone,
                'done_by'           => $user->id,
                'note'              => $data->note,
                'scheduled_time'    => $scheduledTime->addMinutes(60),    
             ]);
        }

       return $nursingChart->update([
           'time_done'  => Carbon::now(),
           'not_done'   => $data->notDone,
           'done_by'    => $user->id,
           'note'       => $data->note,       
           'status'     => true,

        ]);

    }

    public function removeRecord(NursingChart $nursingChart): NursingChart
    {
       $nursingChart->update([
           'time_done'  => null,
           'not_done'   => null,
           'done_by'    => null,
           'note'       => null,       
           'status'     => false,

        ]);

        return $nursingChart;
    }

    public function getPaginatedNursingCharts(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'scheduled_time';
        $orderDir   =  'asc';
        $query = $this->nursingChart->select('id', 'user_id', 'care_prescribed', 'scheduled_time', 'created_at', 'status')->with([
                        'user:id,username',
                    ]);

        if (! empty($params->searchTerm)) {
            return $this->nursingChart
                        ->whereRelation('prescription.resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->where('prescription_id', $data->prescriptionId)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getLoadByPrescriptionsTransformer(): callable
    {
       return  function (NursingChart $nursingChart) {
            return [
                'id'                => $nursingChart->id,
                'service'           => $nursingChart->care_prescribed,
                'scheduledTime'     => (new Carbon($nursingChart->scheduled_time))->format('g:iA D dS'),
                'chartedBy'         => $nursingChart->user->username,
                'chartedAt'         => (new Carbon($nursingChart->created_at))->format('d/m/y g:ia'),
                'given'             => $nursingChart->status
            ];
         };
    }

    public function getUpcomingNursingChart(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'scheduled_time';
        $orderDir   =  'asc';
        $query = $this->nursingChart->select('id', 'visit_id', 'user_id', 'prescription_id', 'schedule_count', 'scheduled_time', 'time_done', 'not_done', 'done_by')->with([
            'user:id,username',
            'prescription' => function($query){
                $query->select('id', 'resource_id', 'note', 'discontinued', )
                ->with(['resource:id,name'])
                ->withCount(['nursingCharts as nursingChartsCount']);
            },
            'visit' => function($query){
                $query->select('id', 'ward', 'bed_no', 'patient_id', 'ward_id', 'admission_status')
                    ->with([
                        'patient' => function($query){
                                        $query->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no')
                                        ->with(['flaggedBy:id,username']);
                                    },
                        'wards:id,visit_id,short_name,bed_number'
                    ]);
            },
            'doneBy:id,username',
        ]);

        if (! empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            return $query->where('status', false)
                        ->whereRelation('visit', 'discharge_reason', null)
                        ->where(function (Builder $query){
                            $query->whereRelation('visit', 'admission_status', '=', 'Inpatient')
                            ->orWhereRelation('visit', 'admission_status', '=','Observation');
                        })
                        ->where(function (Builder $query) use($searchTerm) {
                            $query->WhereRelation('prescription.resource', 'name', 'LIKE', $searchTerm)
                            ->orWhereRelation('visit.patient', 'first_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('visit.patient', 'last_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('visit.patient', 'card_no', 'LIKE', $searchTerm)
                            ->orWhereRelation('visit.sponsor', 'name', 'LIKE', $searchTerm);
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->where('status', false)
                    ->whereRelation('prescription', 'discontinued', false)
                    ->whereRelation('visit', 'discharge_reason', null)
                    ->where(function (Builder $query){
                        $query->whereRelation('visit', 'admission_status', '=', 'Inpatient')
                        ->orWhereRelation('visit', 'admission_status', '=','Observation');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function upcomingNursingChartsTransformer(): callable
    {
       return  function (NursingChart $nursingChart) {
            return [
                'id'                => $nursingChart->id,
                'patient'           => $nursingChart->visit->patient->patientId(),
                'status'            => $nursingChart->visit->admission_status,
                'ward'              => $nursingChart->visit->ward ? $this->helperService->displayWard($nursingChart->visit) : '',
                'wardId'            => $visit->ward_id ?? '',
                'wardPresent'       => $nursingChart->visit->wards?->visit_id == $nursingChart->visit->id,
                'care'              => $nursingChart->prescription->resource->name ?? '',
                'instruction'       => $nursingChart->prescription->note ?? '',
                'chartedBy'         => $nursingChart->user->username,
                'date'              => (new Carbon($nursingChart->scheduled_time))->format('jS/M/y'),
                'time'              => (new Carbon($nursingChart->scheduled_time))->format('g:iA'),
                'scheduleCount'     => $nursingChart->schedule_count,
                'count'             => $nursingChart->prescription->nursingCharts->count(),
                'discontinued'      => $nursingChart->prescription->discontinued,
                'rawDateTime'       => $nursingChart->scheduled_time,
                'timeDone'          => $nursingChart->time_done ? (new Carbon($nursingChart->time_done))->format('jS/M/y g:iA') : '',
                'notDone'           => $nursingChart->not_done,
                'doneBy'            => $nursingChart->doneBy?->username ?? '',
                'flagPatient'       => $nursingChart->visit->patient->flag,
                'flagReason'        => $nursingChart->visit->patient?->flag_reason,
                'flaggedBy'         => $nursingChart->visit->patient->flaggedBy?->username,
                'flaggedAt'         => $nursingChart->visit->patient->flagged_at ? (new Carbon($nursingChart->visit->patient->flagged_at))->format('d/m/y g:ia') : '',
            ];
         };
    }
}