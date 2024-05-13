<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\MedicationChart;
use App\Models\NursingChart;
use App\Models\Prescription;
use App\Models\Resource;
use App\Models\ThirdParty;
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
        private readonly PaymentService $paymentService,
        private readonly CapitationPaymentService $capitationPaymentService
        )
    {
    }

    public function createPrescription(Request $data, Resource $resource, User $user): Prescription
    {
        return DB::transaction(function () use($data, $resource, $user) {
            $bill = 0;
            $nhisBill = fn($value)=>$value/10;
            if ($data->quantity){
                $bill = $resource->selling_price * $data->quantity;
            }

            $prescription = $user->prescriptions()->create([
                'resource_id'       => $resource->id,
                'prescription'      => $this->arrangePrescription($data),
                'consultation_id'   => $data->conId,
                'visit_id'          => $data->visitId,
                'qty_billed'        => $this->determineBillQuantity($resource, $data), // $data->quantity ?? 0,
                'qty_dispensed'     => $this->determineDispense($resource, $data),
                'hms_bill'          => $bill,
                'hms_bill_date'     => $bill ? new Carbon() : null,
                'hms_bill_by'       => $bill ? $user->id : null,
                'chartable'         => $resource->sub_category == 'Injectable' ? true : $data->chartable ?? false,
                'note'              => $data->note,
                'doctor_on_call'    => $data->doc
            ]);

            $isNhis = $prescription->visit->sponsor->category_name == 'NHIS';

            $isNhis && $bill ? $prescription->update(['nhis_bill' => $nhisBill($bill)]) : '';

            $totalPayments = $prescription->visit->totalPayments();

            $prescription->visit->update([
                'viewed_at'         => null,
                'total_hms_bill'    => $prescription->visit->totalHmsBills(),
                'total_nhis_bill'   => $isNhis ? $prescription->visit->totalNhisBills() : 0,
                'total_capitation'  => $isNhis ? $prescription->visit->totalPrescriptionCapitations() : 0,
                'total_paid'        => $totalPayments,
                'pharmacy_done_by'  => $resource->category == 'Medications' || $resource->category == 'Consumables' ? null : $prescription->visit->pharmacy_done_by,
                'nurse_done_by'     => $resource->sub_category == 'Injectable' || $resource->category == 'Consumables' ? null : $prescription->visit->nurse_done_by,
                'hmo_done_by'       => null
            ]);

            if ($isNhis){
                $this->paymentService->prescriptionsPaymentSeiveNhis($totalPayments, $prescription->visit->prescriptions);
                $this->capitationPaymentService->seiveCapitationPayment($prescription->visit->sponsor, new Carbon($prescription->created_at));
            } else {
                $this->paymentService->prescriptionsPaymentSeive($totalPayments, $prescription->visit->prescriptions);
            }
            return $prescription;

        });
        
    }

    public function arrangePrescription($data)
    {
        return $data->dose ? $data->dose.$data->unit.' '.$data->frequency.' for '.$data->days.'day(s)' : null;
    }

    public function determineDispense(Resource $resource, $data)
    {
        if ($resource->category == 'Medications' || $resource->category == 'Consumables' || $resource->category == 'Investigations'){
            return 0;
        }
        $resource->stock_level = $resource->stock_level - $data->quantity; 
        $resource->save();
        return $data->quantity;
    }

    public function determineBillQuantity(Resource $resource, $data)
    {
        if ($resource->category == 'Medications' || $resource->category == 'Consumables'){
            return $resource->stock_level - $data->quantity < 0 ? 0 : $data->quantity ?? 0;
        }

        return $data->quantity ?? 0;
    }

    public function getPaginatedInitialPrescriptions(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->prescription
                        ->where('visit_id', $data->visitId)
                        ->where(function(Builder $query) use ($params) {
                            $query->whereRelation('resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('resource', 'sub_category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );

                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->prescription
                    ->where($data->conId ? 'consultation_id': 'visit_id', $data->conId ? $data->conId : $data->visitId)
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
                'quantity'          => $prescription->qty_billed < 1 ? '' : $prescription->qty_billed,
                'by'                => $prescription->user->username,
                'chartable'         => $prescription->chartable ? 'Yes' : 'No',
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
                'patient'           => $prescription->visit->patient->patientId(),
                'sponsor'           => $prescription->visit->sponsor->name,
                'type'              => $prescription->resource->resourceSubCategory->name,
                'requested'         => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                'resource'          => $prescription->resource->name,
                'sponsorCategory'   => $prescription->visit->sponsor->sponsorCategory->name,
                'payClass'          => $prescription->visit->sponsor->sponsorCategory->pay_class,
                'approved'          => $prescription->approved,
                'rejected'          => $prescription->rejected,
                'paid'              => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill,
                'paidNhis'          => $prescription->paid > 0 && $prescription->approved && $prescription->paid >= $prescription->nhis_bill && $prescription->visit->sponsor->sponsorCategory->name == 'NHIS',
                'diagnosis'         => $prescription->consultation?->icd11_diagnosis ??
                                       $prescription->consultation?->provisional_diagnosis ??
                                       $prescription->consultation?->assessment,
                'dr'                => $prescription->user->username,
                'sample'            => $prescription->test_sample,
                'result'            => $prescription->result,
                'sent'              => $prescription->result_date ? (new Carbon($prescription->result_at))->format('d/m/y g:ia') : '',
                'staff'             => $prescription->resultBy->username ?? '',
                'staffFullName'     => $prescription->resultBy?->nameInFull() ?? '',
                'thirdParty'        => ThirdParty::whereRelation('thirdPartyServies','prescription_id', $prescription->id)->first()?->short_name ?? '',
                'removalReason'     => $prescription->dispense_comment ? $prescription->dispense_comment . ' - ' . $prescription->discontinuedBy?->username : ''
            ];
         };
    }

    public function getPaginatedMedications(DataTableQueryParams $params, $data)
    {
        return DB::transaction(function () use ($params, $data) {

            $orderBy    = 'created_at';
            $orderDir   =  'desc';
    
            return $this->prescription
                        ->where($data->conId ? 'consultation_id': 'visit_id', $data->conId ? $data->conId : $data->visitId)
                        ->whereRelation('resource', 'sub_category', 'Injectable')
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        });
    }

    public function getOtherPrescriptions(DataTableQueryParams $params, $data)
    {
        return DB::transaction(function () use ($params, $data) {

            $orderBy    = 'created_at';
            $orderDir   =  'desc';
    
            return $this->prescription
                        ->where($data->conId ? 'consultation_id': 'visit_id', $data->conId ? $data->conId : $data->visitId)
                        ->where(function(Builder $query) {
                            $query->whereRelation('resource', 'category', 'Medical Services')
                            ->orWhereRelation('resource', 'category', 'Consumables')
                            ->orWhereRelation('resource', 'category', 'Medications')
                            ->orWhere('chartable', true);
                        })
                        ->whereRelation('resource', 'sub_category', '!=', 'Injectable')
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        });
    }

    public function getPrescriptionsTransformer(): callable
    {
       return  function (Prescription $prescription) {
            return [
                'id'                    => $prescription->id,
                'prescribedBy'          => $prescription->user->username,
                'resource'              => $prescription->resource->name,
                'resourceCategory'      => $prescription->resource->category,
                'prescription'          => $prescription->prescription ?? '',
                'prescribed'            => (new Carbon($prescription->created_at))->format('D d/m/y g:ia'),
                'prescribedFormatted'   => (new Carbon($prescription->created_at))->format('Y-m-d\TH:i'),
                'qtyBilled'             => $prescription->qty_billed > 0 ? $prescription->qty_billed : '',
                'qtyDispensed'          => $prescription->qty_dispensed,
                'note'                  => $prescription->note,
                'conId'                 => $prescription->consultation?->id,
                'visitId'               => $prescription->visit->id,
                'patient'               => $prescription->visit->patient->patientId(),
                'sponsor'               => $prescription->visit->sponsor->name,
                'sponsorCategory'       => $prescription->visit->sponsor->sponsorCategory->name,
                'payClass'              => $prescription->visit->sponsor->sponsorCategory->pay_class,
                'approved'              => $prescription->approved,
                'rejected'              => $prescription->rejected,
                'chartable'             => $prescription->chartable,
                'discontinued'          => $prescription->discontinued,
                'paid'                  => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill,
                'paidNhis'              => $prescription->paid > 0 && $prescription->approved && $prescription->paid >= $prescription->nhis_bill && $prescription->visit->sponsor->sponsorCategory->name == 'NHIS',
                'doseCount'             => $doseCount = $prescription->medicationCharts->count(),
                'givenCount'            => $givenCount = $prescription->medicationCharts->where('dose_given', '!=', null)->count(),
                'doseComplete'          => $this->completed($doseCount, $givenCount),
                
                'medicationCharts'      => $prescription->medicationCharts->map(fn(MedicationChart $medicationChart)=> [
                    'id'                => $medicationChart->id ?? '',
                    'chartedAt'         => (new Carbon($medicationChart->created_at))->format('D d/m/y g:ia') ?? '',
                    'chartedBy'         => $medicationChart->user->username ?? '',
                    'dosePrescribed'    => $medicationChart->dose_prescribed ?? '',
                    'scheduledTime'     => (new Carbon($medicationChart->scheduled_time))->format('g:ia D d/m/y') ?? '',
                    'givenDose'         => $medicationChart->dose_given ?? 'Not given' ?? '',
                    'timeGiven'         => $medicationChart->time_given ? (new Carbon($medicationChart->time_given))->format('g:ia D d/m/Y') : '',
                    'givenBy'           => $medicationChart->givenBy->username ?? '',
                    'note'              => $medicationChart->not_given ? $medicationChart->not_given.' - '.$medicationChart->note ?? '' : $medicationChart->note ?? '' ,
                    'status'            => $medicationChart->status ?? '',
                    'doseCount'         => $medicationChart->dose_count,
                    'count'             => $medicationChart::where('prescription_id', $medicationChart->prescription->id)->count(),
                    'patient'           => $medicationChart->visit->patient->patientId() ?? ''
                ]),
                'scheduleCount'         => $scheduleCount = $prescription->nursingCharts->count(),
                'doneCount '            => $doneCount = $prescription->nursingCharts->where('time_done', '!=', null)->count(),
                'serviceComplete'       => $this->completed($scheduleCount, $doneCount),
                'prescriptionCharts'    => $prescription->nursingCharts->map(fn(NursingChart $nursingChart)=> [
                    'id'                => $nursingChart->id ?? '',
                    'chartedAt'         => (new Carbon($nursingChart->created_at))->format('D/m/y g:ia') ?? '',
                    'chartedBy'         => $nursingChart->user->username ?? '',
                    'carePrescribed'    => $nursingChart->care_prescribed ?? '',
                    'treatment'         => $nursingChart->prescription->resource->name,
                    'instruction'       => $nursingChart->prescription->note ?? '',
                    'scheduledTime'     => (new Carbon($nursingChart->scheduled_time))->format('g:ia D jS') ?? '',
                    'timeDone'          => $nursingChart->time_done ? (new Carbon($nursingChart->time_done))->format('g:ia D jS') : '',
                    'doneBy'            => $nursingChart->doneBy->username ?? '',
                    'note'              => $nursingChart->note ?? $nursingChart->not_done ?? '',
                    'status'            => $nursingChart->status ?? '',
                    'scheduleCount'     => $nursingChart->schedule_count,
                    'count'             => $nursingChart::where('prescription_id', $nursingChart->prescription->id)->count(),
                    'patient'           => $nursingChart->visit->patient->patientId() ?? ''
                ]),
            ];
         };
    }

    public function completed($count1, $count2): bool
    {
        return $count1 !== 0 && $count1 === $count2;
    }

    public function discontinue(Request $request, Prescription $prescription)
    {
        return $prescription->update([
            'discontinued'      => !$prescription->discontinued,
            'discontinued_by'   => $request->user()->id,
        ]);
    }

    public function getPaginated(DataTableQueryParams $params, $data)
    {
        return DB::transaction(function () use ($params, $data) {

            $orderBy    = 'created_at';
            $orderDir   =  'desc';
    
            return $this->prescription
                        ->where($data->conId ? 'consultation_id': 'visit_id', $data->conId ? $data->conId : $data->visitId)
                        ->where(function(Builder $query) {
                            $query->whereRelation('resource', 'category', 'Medications')
                            ->orWhereRelation('resource', 'category', 'Medical Services')
                            ->orWhereRelation('resource', 'category', 'Consumables')
                            ->orWhere('chartable', true);
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        });
    }

    public function getEmergencyPrescriptions(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->prescription
                        ->where(function(Builder $query) use($params) {
                            $query->whereRelation('visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->where(function(Builder $query) {
                            $query->whereRelation('resource', 'category', 'Medications')
                            ->orWhereRelation('resource', 'category', 'Medical Services')
                            ->orWhereRelation('resource', 'category', 'Consumables')
                            ->orWhere('chartable', true);
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->viewer == 'pharmacy'){
            return $this->prescription
                    ->where('consultation_id', null)
                    ->where('qty_dispensed', 0)
                    ->where(function(Builder $query) {
                        $query->whereRelation('resource', 'category', 'Medications')
                        ->orWhereRelation('resource', 'category', 'Medical Services')
                        ->orWhereRelation('resource', 'category', 'Consumables')
                        ->orWhere('chartable', true);
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->prescription
                    ->where('consultation_id', null)
                    ->where(function(Builder $query) {
                        $query->whereRelation('resource', 'category', 'Medications')
                        ->orWhereRelation('resource', 'category', 'Medical Services')
                        ->orWhereRelation('resource', 'category', 'Consumables')
                        ->orWhere('chartable', true);
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

    }

    public function getEmergencyPrescriptionsformer(): callable
    {
       return  function (Prescription $prescription) {
            return [
                'id'                => $prescription->id,
                'visitId'           => $prescription->visit->id,
                'patient'           => $prescription->visit->patient->patientId(),
                'sponsor'           => $prescription->visit->sponsor->name,
                'sponsorCategory'   => $prescription->visit->sponsor->sponsorCategory->name,
                'prescribed'        => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                'item'              => $prescription->resource->name,
                'prescription'      => $prescription->prescription,
                'quantity'          => $prescription->qty_billed,
                'prescribedBy'      => $prescription->user->username,
                'doc'               => $prescription->doctorOnCall?->username,
                'note'              => $prescription->note,
                'admissionStatus'   => $prescription->visit->admission_status,
                'prescribedFormatted'   => (new Carbon($prescription->created_at))->format('Y-m-d\TH:i'),
                'chartable'             => $prescription->chartable,
                'doseCount'             => $doseCount = $prescription->medicationCharts->count(),
                'givenCount'            => $givenCount = $prescription->medicationCharts->where('dose_given', '!=', null)->count(),
                'doseComplete'          => $this->completed($doseCount, $givenCount),
                'medicationCharts'      => $prescription->medicationCharts,
                'qtyBilled'             => $prescription->qty_billed,
                'qtyDispensed'          => $prescription->qty_dispensed,
                //'closed'                => $prescription->visit->closed,
            ];
         };
    }

    public function confirm(Request $request, Prescription $prescription)
    {
        return DB::transaction(function () use( $prescription) {
            $conId = $prescription->visit->consultations->sortByDesc('id')->first()?->id;
            if (!$conId){
                return response('Cannot confirm! A Doctor must consult first!', 403);
            }
    
            $prescription->update([
                'consultation_id' => $conId,
            ]);

            $medicationCharts = $prescription->medicationCharts;
            if ($medicationCharts){
                foreach ($medicationCharts as $medicationChart){
                    $medicationChart->update([
                        'consultation_id' => $conId
                    ]);
                }
            }
        });
    }

    public function processDeletion(Prescription $prescription)
    {
        return DB::transaction(function () use($prescription) {
            $prescriptionToDelete = $prescription;

            if ($prescription->qty_dispensed){
                $resource = $prescription->resource;
                $resource->stock_level = $resource->stock_level + $prescription->qty_dispensed;
    
                $resource->save();
            }

            
            $deleted = $prescription->destroy($prescription->id);
            
            $isNhis = $prescriptionToDelete->visit->sponsor->category_name == 'NHIS';

            $prescriptionToDelete->visit->update([
                'total_hms_bill'    => $prescription->visit->totalHmsBills(),
                'total_nhis_bill'   => $isNhis ? $prescription->visit->totalNhisBills() : 0,//$prescription->visit->total_nhis_bill,
                'total_capitation'  => $isNhis ? $prescription->visit->totalPrescriptionCapitations() : 0//$prescription->visit->total_capitation,
            ]);

            if ($isNhis){
                $this->paymentService->prescriptionsPaymentSeiveNhis($prescription->visit->totalPayments(), $prescription->visit->prescriptions);
                $this->capitationPaymentService->seiveCapitationPayment($prescription->visit->sponsor, new Carbon($prescription->created_at));
            } else {
                $this->paymentService->prescriptionsPaymentSeive($prescription->visit->totalPayments(), $prescription->visit->prescriptions);
            }

            return $deleted;
        });
    }

    public function totalYearlyIncomeFromPrescription($data)
    {
        $currentDate = new Carbon();

        if ($data->date){
            $date = new Carbon($data->date);

            return DB::table('prescriptions')
                            ->selectRaw('SUM(hms_bill) as bill, SUM(hmo_bill) as totalHmoBill, SUM(nhis_bill) as totalNhisBill, SUM(paid + capitation) as paid, MONTH(created_at) as month, MONTHNAME(created_at) as month_name')
                            ->whereYear('created_at', $date->year)
                            ->groupBy('month_name', 'month')
                            ->orderBy('month')
                            ->get();
        }

        return DB::table('prescriptions')
                        ->selectRaw('SUM(hms_bill) as bill, SUM(hmo_bill) as totalHmoBill, SUM(nhis_bill) as totalNhisBill, SUM(paid + capitation) as paid, MONTH(created_at) as month, MONTHNAME(created_at) as month_name')
                        ->whereYear('created_at', $currentDate->year)
                        ->groupBy('month_name', 'month')
                        ->orderBy('month')
                        ->get();
    }
}