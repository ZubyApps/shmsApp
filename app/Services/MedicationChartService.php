<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\MedicationChart;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class MedicationChartService
{
    public function __construct(private readonly MedicationChart $medicationChart)
    {
    }

    public function create(Request $data, User $user): MedicationChart
    {
        $tz = 'Africa/Lagos';
        $interval = CarbonInterval::hours($data->frequency);
        // $start = $data->date ? (new CarbonImmutable($data->date, $tz)) : (new CarbonImmutable($data->date, $tz))->addMinutes(15);
        $start = $data->date ? (new CarbonImmutable($data->date, $tz)) : (new CarbonImmutable($data->date, $tz));
        $end    = $start->addDays($data->days);
        $dates = new CarbonPeriod($start, $interval, $end, CarbonPeriod::EXCLUDE_END_DATE);

        $charts = [];
        $iteration = 0;
        foreach ($dates as $date) {
            $iteration++;
            $charts = $user->medicationCharts()->create([
                'prescription_id'   => $data->prescriptionId,
                'consultation_id'   => $data->conId,
                'visit_id'          => $data->visitId,
                'dose_prescribed'   => $data->dose.$data->unit,
                'scheduled_time'    => new Carbon($date, $tz),
                'dose_count'        => $iteration
            ]);
        }
        

        return $charts;
    }

    public function updateRecord(Request $data, MedicationChart $medicationChart, User $user): MedicationChart
    {
       $medicationChart->update([
           'dose_given' => $data->doseGiven ? $data->doseGiven.$data->unit : null,
           'time_given' => Carbon::now(),
           'not_given'  => $data->notGiven,
           'given_by'   => $user->id,
           'note'       => $data->note,
           'status'     => true,

        ]);

        return $medicationChart;
    }

    public function removeRecord(MedicationChart $medicationChart): MedicationChart
    {
       $medicationChart->update([
           'dose_given' => null,
           'time_given' => null,
           'not_given'  => null,
           'given_by'   => null,
           'note'       => null,       
           'status'     => false,

        ]);

        return $medicationChart;
    }

    public function getPaginatedMedicationCharts(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'scheduled_time';
        $orderDir   =  'asc';

        if (! empty($params->searchTerm)) {
            return $this->medicationChart
                        ->whereRelation('prescription.resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->medicationChart
                    ->where('prescription_id', $data->prescriptionId)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getLoadByPrescriptionsTransformer(): callable
    {
       return  function (MedicationChart $medicationChart) {
            return [
                'id'                => $medicationChart->id,
                'dose'              => $medicationChart->dose_prescribed,
                'scheduledTime'     => (new Carbon($medicationChart->scheduled_time))->format('g:iA D dS'),
                'chartedBy'         => $medicationChart->user->username,
                'chartedAt'         => (new Carbon($medicationChart->created_at))->format('d/m/y g:ia'),
                'given'             => $medicationChart->status
            ];
         };
    }

    public function getUpcomingMedications(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'scheduled_time';
        $orderDir   =  'asc';

        if (! empty($params->searchTerm)) {
            return $this->medicationChart
                        ->where('status', false)
                        ->whereRelation('visit', 'discharge_reason', null)
                        // ->whereRelation('visit', 'nurse_done_by', null)
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

        return $this->medicationChart
                    ->where('status', false)
                    ->whereRelation('prescription', 'discontinued', false)
                    ->whereRelation('visit', 'discharge_reason', null)
                    // ->whereRelation('visit', 'nurse_done_by', null)
                    ->where(function (Builder $query){
                        $query->whereRelation('visit', 'admission_status', '=', 'Inpatient')
                        ->orWhereRelation('visit', 'admission_status', '=','Observation');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function upcomingMedicationsTransformer(): callable
    {
       return  function (MedicationChart $medicationChart) {
            return [
                'id'                => $medicationChart->id,
                'patient'           => $medicationChart->visit->patient->card_no .' '. $medicationChart->visit->patient->first_name .' '. $medicationChart->visit->patient->middle_name .' '. $medicationChart->visit->patient->last_name,
                'status'            => $medicationChart->visit->admission_status,
                'ward'              => $medicationChart->visit->ward ?? '',
                'bedNo'             => $medicationChart->visit->bed_no ?? '',
                'treatment'         => $medicationChart->prescription->resource->name ?? '',
                'prescription'      => $medicationChart->prescription->prescription ?? '',
                'dose'              => $medicationChart->dose_prescribed ?? '',
                'chartedBy'         => $medicationChart->user->username,
                'date'              => (new Carbon($medicationChart->scheduled_time))->format('jS/M/y'),
                'time'              => (new Carbon($medicationChart->scheduled_time))->format('g:iA'),
                'doseCount'         => $medicationChart->dose_count,
                'count'             => $medicationChart::where('prescription_id', $medicationChart->prescription->id)->count(),
                'discontinued'      => $medicationChart->prescription->discontinued,
                'rawDateTime'       => $medicationChart->scheduled_time
            ];
         };
    }
}