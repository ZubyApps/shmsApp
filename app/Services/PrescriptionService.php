<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\MedicationChart;
use App\Models\Prescription;
use App\Models\Resource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PrescriptionService
{
    public function __construct(private readonly Prescription $prescription, private readonly Resource $resource)
    {
    }

    public function createFromDoctors(Request $data, User $user): Prescription
    {
        $bill = null;
        if ($data->quantity){
            $resource = $this->resource->find($data->resource);
            $bill = $resource->selling_price * $data->quantity;
        }

        return $user->prescriptions()->create([
            'resource_id'        => $data->resource,
            'prescription'       => $data->prescription,
            'consultation_id'    => $data->conId,
            'visit_id'           => $data->visitId,
            'qty_billed'         => $data->quantity,
            'bill'               => $bill,
            'bill_date'          => $bill ? new Carbon() : null,
            'note'               => $data->note
        ]);
    }

    public function getPaginatedInitialPrescriptions(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->prescription
                        ->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('prescription.resource.rescurceSubCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->prescription
                    ->where('consultation_id', $data->conId)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getInitialLoadTransformer(): callable
    {
       return  function (Prescription $prescription) {
            return [
                'id'                => $prescription->id,
                'prescribed'        => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                'resource'          => $prescription->resource->name,
                'prescription'      => $prescription->prescription,
                'quantity'          => $prescription->qty_billed,
                'by'                => $prescription->user->username,
                'note'              => $prescription->note
            ];
         };
    }

    public function getPaginatedLabRequests(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        return $this->prescription
                    ->where($data->conId ? 'consultation_id': 'visit_id', $data->conId ? $data->conId : $data->visitId)
                    ->whereRelation('resource.resourceSubCategory.resourceCategory', 'name', 'Investigations')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getLabTransformer(): callable
    {
       return  function (Prescription $prescription) {
            return [
                'id'                => $prescription->id,
                'type'              => $prescription->resource->resourceSubCategory->name,
                'requested'         => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                'resource'          => $prescription->resource->name,
                'diagnosis'         => $prescription->consultation->icd11_diagnosis,
                'dr'                => $prescription->user->username,
                'result'            => $prescription->result,
                'sent'              => $prescription->result_date ? (new Carbon($prescription->result_at))->format('d/m/y g:ia') : '',
                'staff'             => $prescription->resultBy->username ?? '',
                'doc'               => $prescription->doc ?? '',
            ];
         };
    }

    public function getPaginatedTreatmentRequests(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        return $this->prescription
                    ->where('consultation_id', $data->conId)
                    ->whereRelation('resource.resourceSubCategory.resourceCategory', 'name', 'Medication')
                    ->orWhereRelation('resource.resourceSubCategory.resourceCategory', 'name', 'Medical Service')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getTreatmentTransformer(): callable
    {
       return  function (Prescription $prescription) {
            return [
                'id'                    => $prescription->id,
                'prescribedBy'          => $prescription->user->username,
                'resource'              => $prescription->resource->name,
                'prescription'          => $prescription->prescription,
                'prescribed'            => (new Carbon($prescription->created_at))->format('D/m/y g:ia'),
                'prescribedFormatted'   => (new Carbon($prescription->created_at))->format('Y-m-d\TH:i'),
                'billed'                => $prescription->bill_date ? (new Carbon($prescription->bill_date))->format('d/m/y g:ia') : '',
                'conId'                 => $prescription->consultation->id,
                'visitId'               => $prescription->visit->id,
                'chart'                 => $prescription->medicationCharts->map(fn(MedicationChart $medicationChart)=> [
                    'id'                => $medicationChart->id ?? '',
                    'chartedAt'         => (new Carbon($medicationChart->created_at))->format('D/m/y g:ia') ?? '',
                    'chartedBy'         => $medicationChart->user->username ?? '',
                    'dosePrescribed'    => $medicationChart->dose_prescribed ?? '',
                    'scheduledTime'     => (new Carbon($medicationChart->scheduled_time))->format('g:ia D jS') ?? '',
                    'givenDose'         => $medicationChart->dose_given ?? '',
                    'timeGiven'         => $medicationChart->time_given ? (new Carbon($medicationChart->time_given))->format('g:ia D jS') : '',
                    'givenBy'           => $medicationChart->givenBy->username ?? '',
                    'note'              => $medicationChart->note ?? '',
                    'status'            => $medicationChart->status ?? '',
                    'doseCount'         => $medicationChart->dose_count,
                    'count'             => $medicationChart::where('prescription_id', $medicationChart->prescription->id)->count(),
                    'patient'           => $medicationChart->visit->patient->patientId() ?? ''
                ]),
            ];
         };
    }

    public function updateLabResultRecord(Request $data, Prescription $prescription, User $user): Prescription
    {
       $prescription->update([
           'result'         => $data->result,
           'result_date'    => Carbon::now(),
           'result_by'      => $user->id,

        ]);

        return $prescription;
    }

    public function removeLabResultRecord(Prescription $prescription): Prescription
    {
       $prescription->update([
        'result'         => null,
        'result_date'    => null,
        'result_by'      => null,

        ]);

        return  $prescription;
    }

}