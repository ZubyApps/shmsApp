<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\NursingChart;
use App\Models\User;
use App\Models\Ward;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

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

    public function create(Request $data, User $user)
    {
        $tz = 'Africa/Lagos';
        $hours    = strtolower($data->intervals) == 'hours';
        $interval = $hours ? CarbonInterval::minutes($data->frequency) : CarbonInterval::hours($data->frequency);
        $start    = (new CarbonImmutable($data->date, $tz));
        $end      = $hours ? $start->addHours($data->value) : $start->addDays($data->value);
        $dates    = new CarbonPeriod($start, $interval, $end, CarbonPeriod::EXCLUDE_END_DATE);

        if (count($dates) > 120) {
            return response()->json(
                ['errors' => [
                    'frequency' => ['This frequency may be too frequent'],
                    'intervals' => ['or the hours/days are too many']
            ]], 422);
        }

        $charts = [];
        $iteration = 0;
        foreach ($dates as $date) {
            $iteration++;
            $charts = $user->nursingCharts()->create([
                'prescription_id'   => $data->prescriptionId,
                'consultation_id'   => $data->conId,
                'visit_id'          => $data->visitId,
                'care_prescribed'   => $data->service,
                'scheduled_time'    => new Carbon($date, $tz),
                'schedule_count'    => $iteration
            ]);
        }
        
        if ($data->date){
            $date = (new CarbonImmutable($data->date, $tz));
            $date < Carbon::now($tz) ? $reason = 'Charted backward' : $reason = 'Charted Forward';
            $data->merge(['reason' => $reason]);
            $this->prescriptionService->hold($data, $charts->prescription, $user);
        }

        return $charts;
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

        if (! empty($params->searchTerm)) {
            return $this->nursingChart
                        ->whereRelation('prescription.resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->nursingChart
                    ->where('prescription_id', $data->prescriptionId)
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

        if (! empty($params->searchTerm)) {
            return $this->nursingChart
                        ->where('status', false)
                        ->whereRelation('visit', 'discharge_reason', null)
                        ->where(function (Builder $query){
                            $query->whereRelation('visit', 'admission_status', '=', 'Inpatient')
                            ->orWhereRelation('visit', 'admission_status', '=','Observation');
                        })
                        ->where(function (Builder $query) use($params) {
                            $query->WhereRelation('prescription.resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('visit.sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->nursingChart
                    ->where('status', false)
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
        $ward = $this->ward->where('id', $nursingChart->visit->ward)->first();
            return [
                'id'                => $nursingChart->id,
                'patient'           => $nursingChart->visit->patient->card_no .' '. $nursingChart->visit->patient->first_name .' '. $nursingChart->visit->patient->middle_name .' '. $nursingChart->visit->patient->last_name,
                'status'            => $nursingChart->consultation->admission_status,
                // 'ward'              => $nursingChart->consultation->ward ?? '',
                // 'bedNo'             => $nursingChart->consultation->bed_no ?? '',
                'ward'              => $ward ? $this->helperService->displayWard($ward) : '',
                'wardId'            => $visit->ward ?? '',
                'wardPresent'       => $ward?->visit_id == $nursingChart->visit->id,
                'care'              => $nursingChart->prescription->resource->name ?? '',
                'instruction'       => $nursingChart->prescription->note ?? '',
                'chartedBy'         => $nursingChart->user->username,
                'date'              => (new Carbon($nursingChart->scheduled_time))->format('jS/M/y'),
                'time'              => (new Carbon($nursingChart->scheduled_time))->format('g:iA'),
                'scheduleCount'     => $nursingChart->schedule_count,
                'count'             => $nursingChart::where('prescription_id', $nursingChart->prescription->id)->count(),
                'discontinued'      => $nursingChart->prescription->discontinued,
                'rawDateTime'       => $nursingChart->scheduled_time,
                'timeDone'          => $nursingChart->time_done ? (new Carbon($nursingChart->time_done))->format('jS/M/y g:iA') : '',
                'notDone'           => $nursingChart->not_done,
                'doneBy'            => $nursingChart->doneBy?->username ?? '',
                'flagPatient'       => $nursingChart->visit->patient->flag,
                'flagReason'        => $nursingChart->visit->patient?->flag_reason,
            ];
         };
    }
}