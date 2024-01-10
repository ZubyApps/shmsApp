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
use Illuminate\Support\Facades\DB;

class PrescriptionService
{
    public function __construct(
        private readonly Prescription $prescription, 
        private readonly Resource $resource,
        private readonly PaymentService $paymentService
        )
    {
    }

    public function createFromDoctors(Request $data, User $user): Prescription
    {
        return DB::transaction(function () use($data, $user) {
            $bill = null;
            if ($data->quantity){
                $resource = $this->resource->find($data->resource);
                $bill = $resource->selling_price * $data->quantity;
            }

            $prescription = $user->prescriptions()->create([
                'resource_id'       => $data->resource,
                'prescription'      => $data->prescription,
                'consultation_id'   => $data->conId,
                'visit_id'          => $data->visitId,
                'qty_billed'        => $data->quantity,
                'hms_bill'          => $bill,
                'hms_bill_date'     => $bill ? new Carbon() : null,
                'hms_bill_by'       => $bill ? $user->id : null,
                'note'              => $data->note
            ]);

            $prescription->visit->update([
                'viewed_at'     => null,
                'viewed_by'     => null,
                'total_bill'    => $data->quantity ? $prescription->visit->totalBills() : ($prescription->visit->totalBills() - $bill)
            ]);

            if ($prescription->visit->sponsor->sponsorCategory->name == 'NHIS'){
                $this->paymentService->prescriptionsPaymentSeiveNhis($prescription->visit->totalPayments(), $prescription->visit->prescriptions);
            } else {
                $this->paymentService->prescriptionsPaymentSeive($prescription->visit->totalPayments(), $prescription->visit->prescriptions);
            }
            return $prescription;

        });
        
    }

    public function getPaginatedInitialPrescriptions(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->prescription
                        ->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('resource', 'sub_category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
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
                    ->whereRelation('resource', 'category', 'Investigations')
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
                    ->where(function(Builder $query) {
                        $query->whereRelation('resource', 'category', 'Medications')
                        ->orWhereRelation('resource', 'category', 'Medical Services')
                        ->orWhereRelation('resource', 'category', 'Consumables');
                    })
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
                'qtyBilled'             => $prescription->qty_billed,
                'qtyDispensed'          => $prescription->qty_dispensed,
                'conId'                 => $prescription->consultation->id,
                'visitId'               => $prescription->visit->id,
                'credit'                => $prescription->visit->sponsor->sponsorCategory->pay_class,
                'approved'              => $prescription->approved,
                'rejected'              => $prescription->rejected,
                'paid'                  => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill,
                'paidNhis'              => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill/10 && $prescription->visit->sponsor->sponsorCategory->name == 'NHIS',
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
           'test_sample'    => $data->sample,
           'result'         => $data->result,
           'result_date'    => Carbon::now(),
           'result_by'      => $user->id,

        ]);

        return $prescription;
    }

    public function removeLabResultRecord(Prescription $prescription): Prescription
    {
       $prescription->update([
        'test_sample'    => null,
        'result'         => null,
        'result_date'    => null,
        'result_by'      => null,

        ]);

        return  $prescription;
    }
}