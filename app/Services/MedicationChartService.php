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
        // dd($interval->cascade()->format('%H:%I:%s'), $start->format('d/m/y g:ia'), $end->format('d/m/y g:ia'), $dates->toArray());

        $charts = [];
        foreach ($dates as $date) {
            $charts = $user->medicationCharts()->create([
                'prescription_id'   => $data->prescriptionId,
                'consultation_id'   => $data->conId,
                'visit_id'          => $data->visitId,
                'dose_prescribed'   => $data->dose.$data->unit,
                'scheduled_time'    => new Carbon($date, $tz)
            ]);
        }
        

        return $charts;
    }

    public function updateRecord(Request $data, MedicationChart $medicationChart, User $user): MedicationChart
    {
        $tz = 'Africa/Lagos';

       $medicationChart->update([
           'dose_given' => $data->doseGiven.$data->unit,
           'time_given' => Carbon::now($tz),
           'given_by'   => $user->id,
           'status'     => true,

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
}