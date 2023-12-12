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
use DatePeriod;
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
        $start = CarbonImmutable::now($tz)->addMinutes(10);
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
           'dose_given' => $data->doseGiven.$data->unit,
           'time_given' => Carbon::now(),
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
                        ->whereRelation('prescription.resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->medicationChart
                    ->where('status', false)
                    ->whereRelation('consultation', 'admission_status', '=', 'Inpatient')
                    ->orWhereRelation('visit.consultations', 'admission_status', '=','Observation')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function upcomingMedicationsTransformer(): callable
    {
       return  function (MedicationChart $medicationChart) {
            return [
                'id'                => $medicationChart->id,
                'patient'           => $medicationChart->visit->patient->card_no .' '. $medicationChart->visit->patient->first_name .' '. $medicationChart->visit->patient->middle_name .' '. $medicationChart->visit->patient->last_name,
                'status'            => $medicationChart->consultation->admission_status,
                'ward'              => $medicationChart->consultation->ward ?? '',
                'treatment'         => $medicationChart->prescription->resource->name ?? '',
                'prescription'      => $medicationChart->prescription->prescription ?? '',
                'dose'              => $medicationChart->dose_prescribed ?? '',
                'chartedBy'         => $medicationChart->user->username,
                'date'              => (new Carbon($medicationChart->scheduled_time))->format('jS/M/y'),
                'time'              => (new Carbon($medicationChart->scheduled_time))->format('g:iA'),
                'doseCount'         => $medicationChart->dose_count,
                'count'             => $medicationChart::where('prescription_id', $medicationChart->prescription->id)->count()
            ];
         };
    }
}