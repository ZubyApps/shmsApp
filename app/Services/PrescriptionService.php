<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\MedicationChart;
use App\Models\NursingChart;
use App\Models\Prescription;
use App\Models\Resource;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrescriptionService
{
    public function __construct(
        private readonly Prescription $prescription, 
        private readonly Resource $resource,
        private readonly PaymentService $paymentService,
        private readonly CapitationPaymentService $capitationPaymentService,
        private readonly ProcedureService $procedureService
        )
    {
    }

    public function createPrescription(Request $data, Resource $resource, User $user): Prescription
    {
        return DB::transaction(function () use($data, $resource, $user) {
            $bill = 0;
            $nhisBill = fn($value)=>$value/10;
            $resourceSubCat = $resource->sub_category;

            $visit = Visit::findOrFail($data->visitId);
            $sponsor = $visit?->sponsor;

            if ($data->quantity){
                $bill = $resource->getSellingPriceForSponsor($sponsor) * $data->quantity;
            }

            $prescription = $user->prescriptions()->create([
                'resource_id'       => $resource->id,
                'prescription'      => $this->arrangePrescription($data),
                'consultation_id'   => $data->conId,
                'visit_id'          => $data->visitId,
                'qty_billed'        => $this->determineBillQuantity($resource, $data),
                'qty_dispensed'     => $this->determineDispense($resource, $data),
                'hms_bill'          => $bill,
                'hms_bill_date'     => $data->quantity ? new Carbon() : null,
                'hms_bill_by'       => $data->quantity ? $user->id : null,
                'chartable'         => $resourceSubCat == 'Injectable' ? true : $data->chartable ?? false,
                'note'              => $data->note,
                'route'             => $data->route,
                'doctor_on_call'    => $data->doc
            ]);

            if ($resourceSubCat == 'Procedure' || $resourceSubCat == 'Operation'){
                $this->procedureService->create($prescription, $user);
            }

            $isNhis = $sponsor->category_name == 'NHIS';

            if ($isNhis && $bill > 0){
                $prescription->update(['nhis_bill' => $nhisBill($bill)]);
            } 

            $totalPayments = $visit->totalPayments();

            $visit->update([
                'total_hms_bill'    => $visit->totalHmsBills(),
                'total_nhis_bill'   => $isNhis ? $visit->totalNhisBills() : 0,
                'total_capitation'  => $isNhis ? $visit->totalPrescriptionCapitations() : 0,
                'total_paid'        => $totalPayments,
                'pharmacy_done_by'  => $resource->category == 'Medications' || $resource->category == 'Consumables' ? null : $visit->pharmacy_done_by,
                'nurse_done_by'     => $resourceSubCat == 'Injectable' || $resource->category == 'Consumables' ? null : $visit->nurse_done_by,
                'hmo_done_by'       => null
            ]);

            if ($isNhis){
                $this->paymentService->prescriptionsPaymentSeiveNhis($totalPayments, $visit->prescriptions);
                $this->capitationPaymentService->seiveCapitationPayment($visit->sponsor, new Carbon($prescription->created_at));
            } else {
                $this->paymentService->prescriptionsPaymentSeive($totalPayments, $visit->prescriptions);
            }
            
            return $prescription->load('procedure');

        }, 2);
        
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
        $query = $this->prescription::with([
            'resource', 
            'user', 
            'thirdPartyServices.thirdParty', 
            'procedure', 
        ]);

        if (! empty($params->searchTerm)) {
            return $query->where('visit_id', $data->visitId)
                        ->where(function(Builder $query) use ($params) {
                            $query->whereRelation('resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('resource', 'sub_category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );

                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->where($data->conId ? 'consultation_id': 'visit_id', $data->conId ? $data->conId : $data->visitId)
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
                'category'          => $prescription->resource->category,
                'stock'             => $prescription->resource->stock_level,
                'prescription'      => $prescription->prescription,
                'quantity'          => $prescription->qty_billed < 1 ? '' : $prescription->qty_billed,
                'hmsBill'           => $prescription->hms_bill ?? '',
                'by'                => $prescription->user->username,
                'chartable'         => $prescription->chartable ? 'Yes' : 'No',
                'note'              => $prescription->note,
                'route'             => $prescription->route,
                'thirdParty'        => $prescription->thirdPartyServices->sortDesc()->first()?->thirdParty->short_name ?? '',
                'procedure'         => $prescription->procedure,
            ];
         };
    }

    public function getPaginatedLabRequests(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query = $this->prescription::with([
            'resource', 
            'user', 
            'thirdPartyServices.thirdParty', 
            'visit' => function($query) {
                $query->with(['sponsor.sponsorCategory', 'patient']);
            },
            'consultation',
            'resultBy',

        ]);

        return $query->where($data->conId ? 'consultation_id': 'visit_id', $data->conId ? $data->conId : $data->visitId)
                    ->whereRelation('resource', 'category', 'Investigations')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getLabTransformer(): callable
    {
       return  function (Prescription $prescription) {
            return [
                'id'                => $prescription->id,
                'patient'           => $prescription->visit->patient->patientFullInfo(),
                'sponsor'           => $prescription->visit->sponsor->name,
                'type'              => $prescription->resource->category,
                'requested'         => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                'resource'          => $prescription->resource->name,
                'sponsorCategory'   => $prescription->visit->sponsor->category_name,
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
                'sent'              => $prescription->result_date ? (new Carbon($prescription->result_date))->format('d/m/y g:ia') : '',
                'staff'             => $prescription->resultBy->username ?? '',
                'staffFullName'     => $prescription->resultBy?->nameInFull() ?? '',
                'thirdParty'        => $prescription->thirdPartyServices->sortDesc()->first()?->thirdParty->short_name,
                'removalReason'     => $prescription->dispense_comment ? $prescription->dispense_comment . ' - ' . $prescription->discontinuedBy?->username : ''
            ];
         };
    }

    public function getPaginatedMedications(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        $query = $this->prescription::with([
            'resource', 
            'visit',
            'consultation',
            'medicationCharts.user',
            'medicationCharts.visit',
            'medicationCharts.givenBy',
            'nursingCharts.user',
            'user',
            'heldBy',
            'visit.sponsor',
            'visit.patient',
        ]);

        return $query->where($data->conId ? 'consultation_id': 'visit_id', $data->conId ? $data->conId : $data->visitId)
                    ->whereRelation('resource', 'sub_category', 'Injectable')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getOtherPrescriptions(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query = $this->prescription::with([
            'resource', 
            'user', 
            'visit.sponsor.sponsorCategory',
            'visit.patient',
            'consultation',
            'nursingCharts.user',
            'nursingCharts.visit',
            'medicationCharts.user',
            'heldBy',
            'nursingCharts.doneBy'     
        ]);

        return $query->where($data->conId ? 'consultation_id': 'visit_id', $data->conId ? $data->conId : $data->visitId)
                    ->where(function(Builder $query) {
                        $query->whereRelation('resource', 'category', 'Medical Services')
                        ->orWhereRelation('resource', 'category', 'Consumables')
                        ->orWhereRelation('resource', 'category', 'Medications')
                        ->orWhere('chartable', true);
                    })
                    ->whereRelation('resource', 'sub_category', '!=', 'Injectable')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
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
                'qtyBilled'             => $prescription->qty_billed ?? '',
                'qtyDispensed'          => $prescription->qty_dispensed ?? '',
                'held'                  => $prescription->held,
                'heldBy'                => $prescription->heldBy?->username,
                'note'                  => $prescription->note,
                'route'                 => $prescription->route,
                'conId'                 => $prescription->consultation?->id,
                'visitId'               => $prescription->visit->id,
                'patient'               => $prescription->visit->patient->patientId(),
                'sponsor'               => $prescription->visit->sponsor->name,
                'sponsorCategory'       => $prescription->visit->sponsor->category_name,
                'payClass'              => $prescription->visit->sponsor->sponsorCategory->pay_class,
                'approved'              => $prescription->approved,
                'rejected'              => $prescription->rejected,
                'chartable'             => $prescription->chartable,
                'discontinued'          => $prescription->discontinued,
                'paid'                  => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill,
                'paidNhis'              => $prescription->paid > 0 && $prescription->approved && $prescription->paid >= $prescription->nhis_bill && $prescription->visit->sponsor->category_name == 'NHIS',
                'doseCount'             => $doseCount = $prescription->medicationCharts->count(),
                'givenCount'            => $givenCount = $prescription->medicationCharts->where('dose_given', '!=', null)->count(),
                'doseComplete'          => $this->completed($doseCount, $givenCount),
                
                'medicationCharts'      => $prescription->medicationCharts->map(fn(MedicationChart $medicationChart) => [
                    'id'                => $medicationChart->id ?? '',
                    'chartedAt'         => (new Carbon($medicationChart->created_at))->format('D d/m/y g:ia') ?? '',
                    'chartedBy'         => $medicationChart->user->username ?? '',
                    'dosePrescribed'    => $medicationChart->dose_prescribed ?? '',
                    'scheduledTime'     => (new Carbon($medicationChart->scheduled_time))->format('g:ia D d/m/y') ?? '',
                    'givenDose'         => $medicationChart->dose_given ?? 'Not yet given' ?? '',
                    'timeGiven'         => $medicationChart->time_given ? (new Carbon($medicationChart->time_given))->format('g:ia D d/m/Y') : '',
                    'givenBy'           => $medicationChart->givenBy->username ?? '',
                    'note'              => $medicationChart->not_given ? $medicationChart->not_given.' - '.$medicationChart->note ?? '' : $medicationChart->note ?? '' ,
                    'status'            => $medicationChart->status ?? '',
                    'doseCount'         => $medicationChart->dose_count,
                    'count'             => $prescription->medicationCharts->count(),
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
                    'treatment'         => $prescription->resource->name,
                    'instruction'       => $prescription->note ?? '',
                    'scheduledTime'     => (new Carbon($nursingChart->scheduled_time))->format('g:ia D jS') ?? '',
                    'timeDone'          => $nursingChart->time_done ? (new Carbon($nursingChart->time_done))->format('g:ia D jS') : '',
                    'doneBy'            => $nursingChart->doneBy->username ?? '',
                    'note'              => $nursingChart->note ?? $nursingChart->not_done ?? '',
                    'status'            => $nursingChart->status ?? '',
                    'scheduleCount'     => $nursingChart->schedule_count,
                    'count'             => $prescription->nursingCharts->count(),
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
        $query = $this->prescription::with([
            'resource', 
            'user', 
            'visit.sponsor',
            'visit.patient',
            'consultation',
            'nursingCharts.user',
            'nursingCharts.visit',
            'medicationCharts.user',
            'heldBy',
            'doctorOnCall'     
        ]);

        if (! empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            return $query->where(function(Builder $query) use($searchTerm) {
                            $query->whereRelation('visit.patient', 'first_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('visit.patient', 'last_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('resource', 'name', 'LIKE', $searchTerm);
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
            return $query->where('consultation_id', null)
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

        return $query->where('consultation_id', null)
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
                'closed'            => $prescription->visit->closed,
                'sponsorCategory'   => $prescription->visit->sponsor->category_name,
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
                'stock'                 => $prescription->resource->stock_level,
                'unit'                  => $prescription->resource->unit_description,
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
            $visit                = $prescription->visit;

            if ($prescription->qty_dispensed){
                $resource = $prescription->resource;
                $resource->stock_level = $resource->stock_level + $prescription->qty_dispensed;
    
                $resource->save();
            }

            
            $deleted = $prescription->destroy($prescription->id);
            
            $isNhis = $visit->sponsor->category_name == 'NHIS';

            $prescriptionToDelete->visit->update([
                'total_hms_bill'    => $visit->totalHmsBills(),
                'total_nhis_bill'   => $isNhis ? $visit->totalNhisBills() : 0,
                'total_capitation'  => $isNhis ? $visit->totalPrescriptionCapitations() : 0,
            ]);

            if ($isNhis){
                $this->paymentService->prescriptionsPaymentSeiveNhis($visit->totalPayments(), $visit->prescriptions);
                $this->capitationPaymentService->seiveCapitationPayment($visit->sponsor, new Carbon($prescription->created_at));
            } else {
                $this->paymentService->prescriptionsPaymentSeive($visit->totalPayments(), $visit->prescriptions);
            }

            return $deleted;
        });
    }

    public function totalYearlyIncomeFromPrescription($data)
    {
        $currentDate = new Carbon();

        if ($data->year){

            return DB::table('prescriptions')
                            ->selectRaw('SUM(hms_bill) as bill, SUM(hmo_bill) as totalHmoBill, SUM(nhis_bill) as totalNhisBill, SUM(paid + capitation) as paid, MONTH(created_at) as month, MONTHNAME(created_at) as month_name')
                            ->whereYear('created_at', $data->year)
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

    public function hold(Request $data, Prescription $prescription, User $user)
    {
        return $prescription->update([
            'held' => $data->reason,
            'held_at' => new Carbon(),
            'held_by' => $user->id,
        ]);
    }

    public function totalYearlyIncomeFromPrescriptionCash($data)
    {
        $currentDate = new Carbon();

        if ($data->year){

            return DB::table('prescriptions')
                            ->selectRaw('SUM(prescriptions.paid + prescriptions.capitation) as cashPaid, MONTH(prescriptions.created_at) as month, MONTHNAME(prescriptions.created_at) as month_name')
                            ->leftJoin('visits', 'prescriptions.visit_id', '=', 'visits.id')
                            ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                            ->whereYear('prescriptions.created_at', $data->year)
                            ->where(function(QueryBuilder $query) {
                                $query->where('sponsors.category_name', 'Individual')
                                ->orWhere('sponsors.category_name', 'Family')
                                ->orWhere('sponsors.category_name', 'NHIS')
                                ->orWhere('sponsors.category_name', 'Retainership');
                            })
                            ->groupBy('month_name', 'month')
                            ->orderBy('month')
                            ->get();
        }

        return DB::table('prescriptions')
                        ->selectRaw('SUM(prescriptions.paid + prescriptions.capitation) as cashPaid, MONTH(prescriptions.created_at) as month, MONTHNAME(prescriptions.created_at) as month_name')
                        ->leftJoin('visits', 'prescriptions.visit_id', '=', 'visits.id')
                        ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                        ->whereYear('prescriptions.created_at', $currentDate->year)
                        ->where(function(QueryBuilder $query) {
                            $query->where('sponsors.category_name', 'Individual')
                            ->orWhere('sponsors.category_name', 'Family')
                            ->orWhere('sponsors.category_name', 'NHIS')
                            ->orWhere('sponsors.category_name', 'Retainership');
                        })
                        ->groupBy('month_name', 'month')
                        ->orderBy('month')
                        ->get();
    }
}