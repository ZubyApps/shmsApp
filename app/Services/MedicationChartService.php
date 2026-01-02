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
use App\Models\Prescription;
use Illuminate\Http\Request;
use App\Models\MedicationChart;
use Illuminate\Support\Facades\DB;
use App\DataObjects\DataTableQueryParams;
use Illuminate\Database\Eloquent\Builder;

class MedicationChartService
{
    public function __construct(
        private readonly MedicationChart $medicationChart, 
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
    //     $start = new CarbonImmutable($data->date, $tz);
    //     $end   = $hours ? $start->addHours($data->intervalsValue) : $start->addDays($data->intervalsValue);
    //     $dates = new CarbonPeriod($start, $interval, $end, CarbonPeriod::EXCLUDE_END_DATE);

    //     if (count($dates) > 120) {
    //         return response()->json(
    //             ['errors' => [
    //                 'frequency' => ['This frequency may be too high'],
    //                 'intervals' => ['or the hours/days are too many']
    //         ]], 422);
    //     }

    //     return DB::transaction(function () use($data, $user, $dates, $tz) {
    //         $iteration = 0;

    //         foreach ($dates as $date) {
    //             $iteration++;
    //             $charts = $user->medicationCharts()->create([
    //                 'prescription_id'   => $data->prescriptionId,
    //                 'consultation_id'   => $data->conId,
    //                 'visit_id'          => $data->visitId,
    //                 'dose_prescribed'   => $data->dose.$data->unit,
    //                 'scheduled_time'    => new Carbon($date, $tz),
    //                 'dose_count'        => $iteration
    //             ]);
    //         }

    //         if ($data->date){
    //             $date = (new CarbonImmutable($data->date, $tz));
    //             $date < Carbon::now($tz) ? $reason = 'Charted backward' : $reason = 'Charted Forward';
    //             $data->merge(['reason' => $reason]);
    //             $this->prescriptionService->hold($data, $charts->prescription, $user);
    //         }

    //         return $charts;
    //     });
    // }

    public function create(Request $data, User $user)
    {
        $tz       = 'Africa/Lagos';
        $isHours  = strtolower($data->intervals) == 'hour(s)';
        $interval = $isHours ? CarbonInterval::minutes($data->frequency) : CarbonInterval::hours($data->frequency);
        
        $start = new CarbonImmutable($data->date, $tz);
        $end   = $isHours ? $start->addHours($data->intervalsValue) : $start->addDays($data->intervalsValue);
        $dates = new CarbonPeriod($start, $interval, $end, CarbonPeriod::EXCLUDE_END_DATE);

        if (count($dates) > 120) {
            return response()->json(['errors' => [
                'frequency' => ['This frequency may be too high'],
                'intervals' => ['or the hours/days are too many']
            ]], 422);
        }

        return DB::transaction(function () use ($data, $user, $dates, $tz) {
            $batch      = [];
            $now        = now($tz); // For timestamps
            $dose       = $data->dose . $data->unit;
            $iteration  = 0;

            // 1. Prepare the data array (No DB hits here)
            foreach ($dates as $date) {
                $iteration++;
                $batch[] = [
                    'user_id'         => $user->id,
                    'prescription_id' => $data->prescriptionId,
                    'consultation_id' => $data->conId,
                    'visit_id'        => $data->visitId,
                    'dose_prescribed' => $dose,
                    'scheduled_time'  => $date->toDateTimeString(),
                    'dose_count'      => $iteration,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }

            // 2. Perform ONE Bulk Insert (Massively faster)
            if (empty($batch)) {
               throw new Exception("No dates generated for charting.");
            }
            
            $inserted = MedicationChart::insert($batch);

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
            return response()->json(['message' => 'Medication charted successfully'], 201);
        });
    }

    public function updateRecord(Request $data, MedicationChart $medicationChart, User $user)
    {
        $scheduledTime = New Carbon($medicationChart->scheduled_time);

        if($data->notGiven == 'Snooze 30 mins'){
            return $medicationChart->update([
                'time_given'        => Carbon::now(),
                'not_given'         => $data->notGiven,
                'given_by'          => $user->id,
                'note'              => $data->note,
                'scheduled_time'    => $scheduledTime->addMinutes(30),    
             ]);
        };

        if($data->notGiven == 'Snooze 60 mins'){
            return $medicationChart->update([
                'time_given'        => Carbon::now(),
                'not_given'         => $data->notGiven,
                'given_by'          => $user->id,
                'note'              => $data->note,
                'scheduled_time'    =>  $scheduledTime->addMinutes(60),    
             ]);
        };

       return $medicationChart->update([
           'dose_given' => $data->doseGiven ? $data->doseGiven.$data->unit : null,
           'time_given' => Carbon::now(),
           'not_given'  => $data->notGiven,
           'given_by'   => $user->id,
           'note'       => $data->note,
           'status'     => true,

        ]);
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
        $query = $this->medicationChart->select('id', 'user_id', 'dose_prescribed', 'scheduled_time', 'created_at', 'status')->with([
            'user:id,username', 
        ]);

        if (! empty($params->searchTerm)) {
            return $query->whereRelation('prescription.resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->where('prescription_id', $data->prescriptionId)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getLoadByPrescriptionsTransformer(): callable
    {
       return  function (MedicationChart $medicationChart) {
            return [
                'id'                => $medicationChart->id,
                'dose'              => $medicationChart->dose_prescribed,
                'scheduledTime'     => (new Carbon($medicationChart->scheduled_time))->format('g:iA D dS M Y'),
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
        $query = $this->medicationChart::with([
            'user:id,username',
            'prescription' => function ($query) {
                $query->select('id', 'prescription', 'discontinued', 'resource_id')
                ->with([
                    'resource:id,name',
                ])
                ->withCount('medicationCharts as medicationChartsCount');
            },
            'visit' => function($query) {
                $query->select('id', 'ward', 'bed_no', 'patient_id', 'ward_id')
                ->with([
                    'patient' => function($query){
                                    $query->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no')
                                    ->with(['flaggedBy:id,username']);
                                },
                    'wards:id,visit_id,short_name,bed_number'
                ]);
            },
            'givenBy:id,username',
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

    public function upcomingMedicationsTransformer(): callable
    {
       return  function (MedicationChart $medicationChart) {
            return [
                'id'                => $medicationChart->id,
                'patient'           => $medicationChart->visit->patient->patientId(),
                'status'            => $medicationChart->visit->admission_status,
                'ward'              => $medicationChart->visit->ward ? $this->helperService->displayWard($medicationChart->visit) : '',
                'wardId'            => $visit->ward_id ?? '',
                'wardPresent'       => $medicationChart->visit->wards?->visit_id == $medicationChart->visit->id,
                'treatment'         => $medicationChart->prescription->resource->name ?? '',
                'prescription'      => $medicationChart->prescription->prescription ?? '',
                'dose'              => $medicationChart->dose_prescribed ?? '',
                'chartedBy'         => $medicationChart->user->username,
                'date'              => (new Carbon($medicationChart->scheduled_time))->format('jS/M/y'),
                'time'              => (new Carbon($medicationChart->scheduled_time))->format('g:iA'),
                'doseCount'         => $medicationChart->dose_count,
                'count'             => $medicationChart->prescription->medicationChartsCount,
                'discontinued'      => $medicationChart->prescription->discontinued,
                'rawDateTime'       => $medicationChart->scheduled_time,
                'timeGiven'         => $medicationChart->time_given ? (new Carbon($medicationChart->time_given))->format('jS/M/y g:iA') : '',
                'notGiven'          => $medicationChart->not_given ?? '',
                'givenBy'           => $medicationChart->givenBy?->username ?? '',
                'flagPatient'       => $medicationChart->visit->patient->flag,
                'flagReason'        => $medicationChart->visit->patient?->flag_reason,
                'flaggedBy'         => $medicationChart->visit->patient->flaggedBy?->username,
                'flaggedAt'         => $medicationChart->visit->patient->flagged_at ? (new Carbon($medicationChart->visit->patient->flagged_at))->format('d/m/y g:ia') : '',
            ];
         };
    }
}