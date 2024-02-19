<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\NursingChart;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class NursingChartService
{
    public function __construct(private readonly NursingChart $nursingChart)
    {
    }

    public function create(Request $data, User $user): NursingChart
    {
        $tz = 'Africa/Lagos';
        $interval = CarbonInterval::hours($data->frequency);
        $start = CarbonImmutable::now($tz)->addMinutes(30);
        $end    = $start->addDays($data->days);
        $dates = new CarbonPeriod($start, $interval, $end, CarbonPeriod::EXCLUDE_END_DATE);

        $charts = [];
        $iteration = 0;
        foreach ($dates as $date) {
            $iteration++;
            $charts = $user->nursingCharts()->create([
                'prescription_id'   => $data->prescriptionId,
                'consultation_id'   => $data->conId,
                'visit_id'          => $data->visitId,
                'care_prescribed'   => $data->dose.$data->unit,
                'scheduled_time'    => new Carbon($date, $tz),
                'schedule_count'    => $iteration
            ]);
        }
        

        return $charts;
    }

    public function updateRecord(Request $data, NursingChart $nursingChart, User $user): NursingChart
    {
       $nursingChart->update([
        //    'dose_given' => $data->doseGiven.$data->unit,
           'time_done'  => Carbon::now(),
           'not_done'   => $data->notDone,
           'done_by'    => $user->id,
           'note'       => $data->note,       
           'status'     => true,

        ]);

        return $nursingChart;
    }

    public function removeRecord(NursingChart $nursingChart): NursingChart
    {
       $nursingChart->update([
        //    'dose_given' => null,
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
                'care'              => $nursingChart->care_prescribed,
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
                        ->where(function (Builder $query){
                            $query->whereRelation('consultation', 'admission_status', '=', 'Inpatient')
                            ->orWhereRelation('visit.consultations', 'admission_status', '=','Observation');
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
                    ->where(function (Builder $query){
                        $query->whereRelation('consultation', 'admission_status', '=', 'Inpatient')
                        ->orWhereRelation('visit.consultations', 'admission_status', '=','Observation');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function upcomingNursingChartsTransformer(): callable
    {
       return  function (NursingChart $nursingChart) {
            return [
                'id'                => $nursingChart->id,
                'patient'           => $nursingChart->visit->patient->card_no .' '. $nursingChart->visit->patient->first_name .' '. $nursingChart->visit->patient->middle_name .' '. $nursingChart->visit->patient->last_name,
                'status'            => $nursingChart->consultation->admission_status,
                'ward'              => $nursingChart->consultation->ward ?? '',
                'care'              => $nursingChart->prescription->resource->name ?? '',
                'instruction'       => $nursingChart->prescription->note ?? '',
                'chartedBy'         => $nursingChart->user->username,
                'date'              => (new Carbon($nursingChart->scheduled_time))->format('jS/M/y'),
                'time'              => (new Carbon($nursingChart->scheduled_time))->format('g:iA'),
                'scheduleCount'     => $nursingChart->schedule_count,
                'count'             => $nursingChart::where('prescription_id', $nursingChart->prescription->id)->count(),
                'discontinued'      => $nursingChart->prescription->discontinued,
                'rawDateTime'       => $nursingChart->scheduled_time
            ];
         };
    }
}