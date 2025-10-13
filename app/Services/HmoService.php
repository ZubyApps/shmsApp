<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Prescription;
use App\Models\Sponsor;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HmoService
{
    public function __construct(
        private readonly Visit $visit, 
        private readonly Prescription $prescription,
        private readonly PayPercentageService $payPercentageService,
        private readonly Sponsor $sponsor,
        private readonly PaymentService $paymentService,
        )
    {
        
    }

    public function getPaginatedVerificationList(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query = $this->visit::with([
            'sponsor', 
            'patient.visits',  
            'doctor', 
            'closedOpenedBy'
        ]);

        if (! empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            return $query->where(function (Builder $query) use($searchTerm) {
                            $query->whereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
                            ->orWhereRelation('sponsor', 'name', 'LIKE', $searchTerm);
                        })
                        ->orWhere('verification_status', 'LIKE', $searchTerm)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query
                    ->where('verified_at', null)
                    ->where(function (Builder $query) use($params) {
                        $query->whereRelation('sponsor', 'category_name', '=', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', '=', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', '=', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getVerificationListTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patientId'         => $visit->patient->id,
                'patient'           => $visit->patient->patientId(),
                'staffId'           => $visit->patient->staff_id ?? '',
                'sex'               => $visit->patient->sex,
                'age'               => $visit->patient->age(),
                'sponsor'           => $visit->sponsor->name,
                'sponsorCategory'   => $visit->sponsor->category_name,
                'flagSponsor'       => $visit->sponsor->flag,
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient?->flag_reason,
                'doctor'            => $visit->doctor->username ?? '',
                'codeText'          => $visit->verification_code ?? '',
                'phone'             => $visit->patient->phone,
                'status'            => $visit->verification_status ?? '',
                '30dayCount'        => $visit->patient->visits->where('consulted', '>', (new Carbon())->subDays(30))->count().' visit(s)',
                'visitType'         => $visit->visit_type
            ];
         };
    }

    public function verify(Request $request, Visit $visit): Visit
    {
       
            $visit->update([
                'verification_status'   => $request->status,
                'verification_code'     => $request->codeText,
                'verified_at'           => $request->status === 'Verified' || $request->status === 'Exponged' ? new Carbon() : null,
                'verified_by'           => $request->user()->id,
            ]);

            return $visit;
    }

    public function getPaginatedAllConsultedHmoVisits(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'consulted';
        $orderDir   =  'desc';
        $query = $this->visit::with([
            'sponsor', 
            'consultations',
            'vitalSigns', 
            'patient', 
            'prescriptions',
            'antenatalRegisteration', 
            'doctor', 
            'closedOpenedBy',
        ])
        ->withCount([
            'prescriptions as labPrescribed' => function (Builder $query) {
            $query->whereRelation('resource', 'category', '=', 'Investigations');
            }, 
            'prescriptions as labDone' => function (Builder $query) {
            $query->whereRelation('resource', 'category', '=', 'Investigations')
                ->where('result_date', '!=', null);
            },
        ])
        ->whereNotNull('consulted');

        if (! empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            return $query->where(function (Builder $query) use($searchTerm) {
                        $query->where('created_at', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
                        ->orWhereRelation('consultations', 'icd11_diagnosis', 'LIKE', $searchTerm)
                        ->orWhereRelation('consultations', 'admission_status', 'LIKE', $searchTerm)
                        ->orWhereRelation('sponsor', 'name', 'LIKE', $searchTerm)
                        ->orWhereRelation('sponsor', 'category_name', 'LIKE', $searchTerm);
                    })
                    
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Outpatient'){
            return $query->where('hmo_done_by', null)
            // ->where('closed', false)
            ->where('admission_status', '=', 'Outpatient')
            ->where('visit_type', '!=', 'ANC')
            ->where(function (Builder $query) {
                $query->whereRelation('sponsor', 'category_name', 'HMO')
                ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                ->orWhereRelation('sponsor', 'category_name', 'Retainership');
            })
            ->orderBy($orderBy, $orderDir)
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Inpatient'){
            return $query->where('hmo_done_by', null)
                    // ->where('closed', false)
                    ->where(function (Builder $query) {
                        $query->where('admission_status', '=', 'Inpatient')
                        ->orWhere('admission_status', '=', 'Observation');
                    })
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
        if ($data->filterBy == 'ANC'){
            return $query->where('hmo_done_by', null)
                    // ->where('closed', false)
                    ->where('visit_type', '=', 'ANC')
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->where('hmo_done_by', null)
                    // ->where('closed', false)
                        ->where(function (Builder $query) {
                            $query->whereRelation('sponsor', 'category_name', 'HMO')
                            ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                            ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                        })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getAllHmoConsultedVisitsTransformer(): callable
    {
       return  function (Visit $visit) {
        $latestConsultation = $visit->consultations->sortDesc()->first();
            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patient'           => $visit->patient->patientId(),
                'age'               => $visit->patient->age(),
                'sex'               => $visit->patient->sex,
                'staffId'           => $visit->patient?->staff_id ?? '',
                'phone'             => $visit->patient?->phone,
                'doctor'            => $visit->doctor?->username,
                'ancRegId'          => $visit->antenatalRegisteration?->id,
                'diagnosis'         => $latestConsultation?->icd11_diagnosis ?? $latestConsultation?->provisional_diagnosis ?? $latestConsultation?->assessment,
                'sponsor'           => $visit->sponsor->name,
                'sponsorCategory'   => $visit->sponsor->category_name,
                'flagSponsor'       => $visit->sponsor->flag,
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient?->flag_reason,
                'vitalSigns'        => $visit->vitalSigns->count(),
                'admissionStatus'   => $latestConsultation?->admission_status,
                'visitType'         => $visit->visit_type,
                'labPrescribed'     => $visit->labPrescribed,
                'labDone'           => $visit->labDone,
                'payPercent'        => $this->payPercentageService->individual_Family($visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'thirtyDayCount'    => $visit->visit_type == 'ANC' ? $visit->consultations->count() : $visit->patient->visits->where('consulted', '>', (new Carbon())->subDays(30))->count().' visit(s)',
                'discharged'        => $visit->discharge_reason,
                'reason'            => $visit->discharge_reason,
                'doctorDoneAt'      => $visit->doctor_done_at ? (new Carbon($visit->doctor_done_at))->format('d/m/y g:ia') : '',
                'viewedAt'          => $visit->viewed_at,
                'viewedBy'          => $visit->viewedBy?->username,
                'hmoDoneBy'         => $visit->hmoDoneBy?->username,
                'closed'            => $visit->closed,
                'closedBy'          => $visit->closedOpenedBy?->username

            ];
         };
    }

    public function getPaginatedAllPrescriptionsRequest(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';
        $query      = $this->prescription::with([
            'visit' => function ($query) {
                $query->with(['sponsor', 'patient', 'payments']);
            },
            'resource.prescriptions',
            'consultation',
            'hmoBillBy',
            'approvedBy',
            'rejectedBy',
            'user',
        ]);

            if (! empty($params->searchTerm)) {
                $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
                return $query->where(function (Builder $query) use($data) {
                        $query->whereRelation('visit.sponsor', 'category_name', ($data->sponsor == 'NHIS' ? '' : 'HMO'))
                        ->orWhereRelation('visit.sponsor', 'category_name', ($data->sponsor == 'NHIS' ? 'NHIS' : ''))
                        ->orWhereRelation('visit.sponsor', 'category_name', ($data->sponsor == 'NHIS' ? '' : 'Retainership'));
                    })
                    ->where(function (Builder $query) use($searchTerm) {
                        $query->whereRelation('visit.sponsor', 'name', 'LIKE', $searchTerm)
                        ->orWhereRelation('visit.patient', 'first_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('visit.patient', 'last_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('visit.patient', 'card_no', 'LIKE', $searchTerm)
                        ->orWhereRelation('resource', 'name', 'LIKE', $searchTerm)
                        ->orWhereRelation('resource.resourceSubCategory', 'name', 'LIKE', $searchTerm)
                        ->orWhereRelation('resource.resourceSubCategory.resourceCategory', 'name', 'LIKE', $searchTerm)
                        ->orWhereRelation('approvedBy', 'username', 'LIKE', $searchTerm)
                        ->orWhereRelation('rejectedBy', 'username', 'LIKE', $searchTerm);
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if ($data->sponsor == 'NHIS'){
                return $query->where('approved', false)
                    ->where('rejected', false)
                    ->whereRelation('visit.sponsor', 'category_name', 'NHIS')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $query->where('approved', false)
                    ->where('rejected', false)
                    ->where(function (Builder $query) {
                        $query->whereRelation('visit.sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('visit.sponsor', 'category_name', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getAllPrescriptionsTransformer(): callable
    {
       return  function (Prescription $prescription) {
        $sponsorCategory = $prescription->visit->sponsor->category_name;
        $flag = $prescription->resource->flag;

            return [
                'id'                => $prescription->id,
                'patient'           => $prescription->visit->patient->patientId(),
                'sponsor'           => $prescription->visit->sponsor->name,
                'sponsorCategory'   => $sponsorCategory,
                'doctor'            => $prescription->user->username,
                'prescribed'        => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                'diagnosis'         => $prescription->consultation?->icd11_diagnosis ?? 
                                       $prescription->consultation?->provisional_diagnosis ?? 
                                       $prescription->consultation?->assessment, 
                'resource'          => $prescription->resource->name,
                'resourcePrice'     => $prescription->resource->selling_price,
                'resourceFlagged'   => str_contains($flag, $sponsorCategory) ? true : false,
                'prescription'      => $prescription->prescription,
                'quantity'          => $prescription->qty_billed,
                'totalQuantity'     => $prescription->resource->prescriptions->where('visit_id', $prescription->visit->id)->sum('qty_billed'),
                'note'              => $prescription->note,
                'hmsBill'           => $prescription->hms_bill ?? '',
                'hmsBillDate'       => $prescription->hms_bill_date ? (new Carbon($prescription->hms_bill_date))->format('d/m/y g:ia') : '',
                'hmoBill'           => $prescription->hmo_bill ?? '',
                'hmoBillBy'         => $prescription->hmoBillBy?->username,
                'paidHms'           => $prescription->visit->totalPayments() ?? '',
                'approved'          => $prescription->approved,
                'approvedBy'        => $prescription->approvedBy?->username,
                'rejected'          => $prescription->rejected,
                'rejectedBy'        => $prescription->rejectedBy?->username,
                'dispensed'         => $prescription->dispensed,
                'hmoDoneBy'         => $prescription->visit->hmoDoneBy?->username,
                'flagSponsor'       => $prescription->visit->sponsor->flag,
                'flagPatient'       => $prescription->visit->patient->flag,
                'flagReason'        => $prescription->visit->patient?->flag_reason,
            ];
         };
    }

    public function approve($data, Prescription $prescription, User $user)
    {
        if ($prescription->approved == true || $prescription->rejected == true){
            return response('Already treated by ' . $prescription->approvedBy?->username ?? $prescription->rejectedBy?->username, 222);
        }

        return DB::transaction(function () use($data, $prescription, $user) {

            $prescription->update([
                'approved'         => true,
                'hmo_note'         => $data->note,
                'approved_by'      => $user->id,
            ]);

            $visit  = $prescription->visit;

            $isNhis = $visit->sponsor->category_name == 'NHIS';

            if ($isNhis){
                $resourceCat = $prescription->resource->category;

                $isNhisBillable = $resourceCat == 'Medications' || $resourceCat == 'Consumables' ;

                $prescription->update(['nhis_bill' => $isNhisBillable ? ($prescription->hms_bill ? $prescription->hms_bill/10 : 0) : 0]);

                $this->paymentService->prescriptionsPaymentSeiveNhis($visit->totalPayments(), $visit->prescriptions);
                
                $visit->update(['total_nhis_bill' => $visit->totalNhisBills()]);
            } else {
                $this->paymentService->prescriptionsPaymentSeive($visit->totalPayments(), $visit->prescriptions);
                $visit->update(['total_hms_bill'    => $prescription->visit->totalHmsBills()]);
            }

            return $prescription;
        });
    }

    public function reject($data, Prescription $prescription, User $user)
    {
        if ($prescription->approved == true || $prescription->rejected == true){
            return response('Already treated by ' . $prescription->rejectedBy?->username ??  $prescription->approvedBy?->username, 222);
        }

        return DB::transaction(function () use($data, $prescription, $user) {

            $prescription->update([
                'rejected'          => true,
                'hmo_note'          => $data->note,
                'rejected_by'       => $user->id,
            ]);

            $visit  = $prescription->visit;

            $isNhis = $visit->sponsor->category_name == 'NHIS';

            if ($isNhis){
                $prescription->update(['nhis_bill' => $prescription->hms_bill]);
                $this->paymentService->prescriptionsPaymentSeiveNhis($visit->totalPayments(), $visit->prescriptions);
                $visit->update(['total_nhis_bill'   => $prescription->visit->totalNhisBills()]);
            } else {
                $this->paymentService->prescriptionsPaymentSeive($visit->totalPayments(), $visit->prescriptions);
                $visit->update(['total_hms_bill'    => $prescription->visit->totalHmsBills()]);
            }

            return $prescription;
        });

    }

    public function reset(Prescription $prescription)
    {
        return DB::transaction(function () use($prescription) {

            $prescription->update([
                'approved'          => false,
                'hmo_note'          => null,
                'approved_by'       => null,
                'rejected'          => false,
                'rejected_by'       => null,
            ]);

            $visit    = $prescription->visit;

            $isNhis = $visit->sponsor->category_name == 'NHIS';

            if ($isNhis){
                $prescription->update(['nhis_bill' => $prescription->hms_bill]);
                $this->paymentService->prescriptionsPaymentSeiveNhis($visit->totalPayments(), $visit->prescriptions);
                $visit->update(['total_nhis_bill'   => $prescription->visit->totalNhisBills()]);
            } else {
                $this->paymentService->prescriptionsPaymentSeive($visit->totalPayments(), $visit->prescriptions);
                $visit->update(['total_hms_bill'    => $prescription->visit->totalHmsBills()]);
            }
    
            return $prescription;
        });

    }

    public function getPaginatedVisitPrescriptionsRequest(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query      = $this->prescription::with([
            'visit' => function ($query) {
                $query->with(['sponsor', 'patient', 'payments']);
            },
            'resource.prescriptions',
            'consultation',
            'hmoBillBy',
            'approvedBy',
            'rejectedBy',
            'user',
        ]);        

            if (! empty($params->searchTerm)) {
                $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
                return $query->where('visit_id', $data->visitId)
                            ->where(function (Builder $query) use($searchTerm) {
                                $query->whereRelation('consultation', 'icd11_diagnosis', 'LIKE', '%' . $searchTerm)
                                ->orWhereRelation('user', 'username', 'LIKE', '%' . $searchTerm)
                                ->orWhereRelation('resource', 'name', 'LIKE', '%' . $searchTerm)
                                ->orWhereRelation('resource.resourceSubCategory', 'name', 'LIKE', '%' . $searchTerm)
                                ->orWhereRelation('resource.resourceSubCategory.resourceCategory', 'name', 'LIKE', '%' . $searchTerm);
                            })
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

        return $query->where('visit_id', $data->visitId)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function saveBill(Request $data, Prescription $prescription, User $user): Prescription
    {
        $prescription->update([
            'hmo_bill'       => $data->bill ?? 0,
            'hmo_bill_date'  => new Carbon(),
            'hmo_bill_by'    => $user->id,
            'hmo_bill_note'  => $data->note
        ]);   
        
        return $prescription;
    }

    public function treat( Visit $visit, User $user)
    {
        return $visit->update([
            'viewed_at' => Carbon::now(),
            'viewed_by' => $user->id,
        ]);
    }

    public function markAsSent( Visit $visit, User $user)
    {
        return $visit->update([
            'hmo_done_by'    => !$visit->hmo_done_by ? $user->id : null,
            'total_hmo_bill' => !$visit->hmo_done_by ? $visit->totalHmoBills() : 0,
        ]);
    }

    public function getSentBillsList(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $current    = Carbon::now();
        $query = $this->visit::with([
            'sponsor', 
            'consultations',
            'patient', 
            'prescriptions',
            'doctor', 
            'closedOpenedBy',
            'hmoDoneBy',
            'payments'
        ])
        ->whereNotNull('consulted');

        if (! empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            if ($data->startDate && $data->endDate){
                return $query->WhereNotNull('hmo_done_by')
                        ->where(function (Builder $query) use($searchTerm) {
                            $query->whereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
                            ->orWhereRelation('sponsor', 'name', 'LIKE', $searchTerm)
                            ->orWhereRelation('sponsor', 'category_name', 'LIKE', $searchTerm);
                            // ->orWhereRelation('hmoDoneBy', 'username', 'LIKE', $searchTerm);
                        })
                        ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if ($data->date){
                $date = new Carbon($data->date);

                return $query->WhereNotNull('hmo_done_by')
                        ->where(function (Builder $query) use($searchTerm) {
                            $query->whereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
                            ->orWhereRelation('sponsor', 'name', 'LIKE', $searchTerm)
                            ->orWhereRelation('sponsor', 'category_name', 'LIKE', $searchTerm);
                            // ->orWhereRelation('hmoDoneBy', 'username', 'LIKE', $searchTerm);
                        })
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            return $query->WhereNotNull('hmo_done_by')
                        ->where(function (Builder $query) use($searchTerm) {
                            $query->whereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
                            ->orWhereRelation('sponsor', 'name', 'LIKE', $searchTerm)
                            ->orWhereRelation('sponsor', 'category_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('hmoDoneBy', 'username', 'LIKE', $searchTerm);
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->startDate && $data->endDate){

            if ($data->filterByOpen){
                return $query->where('hmo_done_by', '!=', null)
                        ->where('closed', false)
                        ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                        ->where(function (Builder $query) {
                            $query->whereRelation('sponsor', 'category_name', 'HMO')
                            ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                            ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $query->where('hmo_done_by', '!=', null)
                    ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->date){
            $date = new Carbon($data->date);

            if ($data->filterByOpen){
                return $query->where('hmo_done_by', '!=', null)
                        ->where('closed', false)
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->where(function (Builder $query) {
                            $query->whereRelation('sponsor', 'category_name', 'HMO')
                            ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                            ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $query->where('hmo_done_by', '!=', null)
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterByOpen){
            return $query->where('hmo_done_by', '!=', null)
                    ->where('closed', false)
                    ->whereMonth('created_at', $current->month)
                    ->whereYear('created_at', $current->year)
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->where('hmo_done_by', '!=', null)
                    ->whereMonth('created_at', $current->month)
                    ->whereYear('created_at', $current->year)
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getSentBillsTransformer(): callable
    {
        return  function (Visit $visit) {
            $latestConsultation = $visit->consultations->sortDesc()->first();
            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patient'           => $visit->patient->patientId(),
                'sponsor'           => $visit->sponsor->name,
                'doctor'            => $visit->doctor->username,
                'diagnosis'         => $latestConsultation?->icd11_diagnosis ?? $latestConsultation?->provisional_diagnosis ?? $latestConsultation?->assessment,
                'sentBy'            => $visit->hmoDoneBy?->username,
                'totalHmsBill'      => $visit->total_hms_bill,
                'totalHmoBill'      => $visit->total_hmo_bill,
                'sponsorCategory'   => $visit->sponsor->category_name,
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'closed'            => $visit->closed,
                'flagSponsor'       => $visit->sponsor->flag,
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient?->flag_reason,
                'visitType'         => $visit->visit_type,
            ];
        };
    }

    public function getReportSummaryTable(DataTableQueryParams $params, $data)
    {
        $current    = Carbon::now();

        if (! empty($params->searchTerm)) {
            return DB::table('visits')
                    ->selectRaw('SUM(visits.total_hms_bill) as totalHmsBill, SUM(visits.total_hmo_bill) as totalHmoBill, SUM(visits.total_paid) as totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) as discount, sponsors.name as sponsor, sponsors.id as id, sponsors.category_name as category, COUNT(DISTINCT visits.id) as visitsCount, MONTHNAME(visits.created_at) as monthName, SUM(CASE WHEN visits.hmo_done_by IS NOT NULL THEN 1 ELSE 0 END) AS billsSent, DATE_FORMAT(visits.created_at, "%m") as month, YEAR(visits.created_at) as year, EXTRACT(YEAR_MONTH FROM visits.created_at) as yearMonth')
                    ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                    ->where('consulted', '!=', null)
                    ->where('hmo_done_by', '!=', null)
                    ->where(function (QueryBuilder $query) {
                        $query->where('sponsors.category_name', 'HMO')
                        ->orWhere('sponsors.category_name', 'NHIS' )
                        ->orWhere('sponsors.category_name', 'Retainership' );
                    })
                    ->where(function (QueryBuilder $query) use ($params){
                        $query->where('sponsors.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('sponsors.category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                    })
                    ->groupBy('yearMonth', 'sponsor', 'category', 'monthName', 'year', 'month', 'id')
                    ->orderBy('month')
                    ->get()
                    ->toArray();
        }

        if ($data->category){
            if ($data->startDate && $data->endDate){
                
                return DB::table('visits')
                            ->selectRaw('SUM(visits.total_hms_bill) as totalHmsBill, SUM(visits.total_hmo_bill) as totalHmoBill, SUM(visits.total_paid) as totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) as discount, sponsors.name as sponsor, sponsors.id as id, sponsors.category_name as category, COUNT(DISTINCT visits.id) as visitsCount, SUM(CASE WHEN visits.hmo_done_by IS NOT NULL THEN 1 ELSE 0 END) AS billsSent, MONTHNAME(visits.created_at) as monthName, YEAR(visits.created_at) as year')
                            ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                            ->where('sponsors.category_name', $data->category)
                            ->where('visits.consulted', '!=', null)
                            ->where('visits.hmo_done_by', '!=', null)
                            ->WhereBetween('visits.created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                            ->groupBy('sponsor', 'id', 'category', 'monthName', 'year')
                            ->orderBy('sponsor')
                            ->orderBy('visitsCount')
                            ->get()
                            ->toArray();
            }

            if ($data->date){
                $date = new Carbon($data->date);

                return DB::table('visits')
                ->selectRaw('SUM(visits.total_hms_bill) as totalHmsBill, SUM(visits.total_hmo_bill) as totalHmoBill, SUM(visits.total_paid) as totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) as discount, sponsors.name as sponsor, sponsors.id as id, sponsors.category_name as category, COUNT(DISTINCT visits.id) as visitsCount, SUM(CASE WHEN visits.hmo_done_by IS NOT NULL THEN 1 ELSE 0 END) AS billsSent, MONTHNAME(visits.created_at) as monthName, YEAR(visits.created_at) as year')
                ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                ->where('sponsors.category_name', $data->category)
                ->where('visits.consulted', '!=', null)
                ->where('visits.hmo_done_by', '!=', null)
                ->whereMonth('visits.created_at', $date->month)
                ->whereYear('visits.created_at', $date->year)
                ->groupBy('sponsor', 'id', 'category', 'monthName', 'year')
                ->orderBy('sponsor')
                ->orderBy('visitsCount')
                ->get()
                ->toArray();
            }

            return DB::table('visits')
                            ->selectRaw('SUM(visits.total_hms_bill) as totalHmsBill, SUM(visits.total_hmo_bill) as totalHmoBill, SUM(visits.total_paid) as totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) as discount, sponsors.name as sponsor, sponsors.id as id, sponsors.category_name as category, COUNT(DISTINCT visits.id) as visitsCount, SUM(CASE WHEN visits.hmo_done_by IS NOT NULL THEN 1 ELSE 0 END) AS billsSent, MONTHNAME(visits.created_at) as monthName, YEAR(visits.created_at) as year')
                            ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                            ->where('visits.consulted', '!=', null)
                            ->where('visits.hmo_done_by', '!=', null)
                            ->where('sponsors.category_name', $data->category)
                            ->whereMonth('visits.created_at', $current->month)
                            ->whereYear('visits.created_at', $current->year)
                            ->groupBy('sponsor', 'id', 'category', 'monthName', 'year')
                            ->orderBy('sponsor')
                            ->orderBy('visitsCount')
                            ->get()
                            ->toArray();
        }

        if ($data->startDate && $data->endDate){
            return DB::table('visits')
                        ->selectRaw('SUM(visits.total_hms_bill) as totalHmsBill, SUM(visits.total_hmo_bill) as totalHmoBill, SUM(visits.total_paid) as totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) as discount, sponsors.name as sponsor, sponsors.id as id, sponsors.category_name as category, COUNT(DISTINCT visits.id) as visitsCount, SUM(CASE WHEN visits.hmo_done_by IS NOT NULL THEN 1 ELSE 0 END) AS billsSent, MONTHNAME(visits.created_at) as monthName, YEAR(visits.created_at) as year')
                        ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                        ->where('visits.consulted', '!=', null)
                        ->where('visits.hmo_done_by', '!=', null)
                        ->WhereBetween('visits.created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                        ->where(function (QueryBuilder $query) {
                            $query->where('sponsors.category_name', 'HMO')
                            ->orWhere('sponsors.category_name', 'NHIS' )
                            ->orWhere('sponsors.category_name', 'Retainership' );
                        })
                        ->groupBy('sponsor', 'id', 'category', 'monthName', 'year')
                        ->orderBy('sponsor')
                        ->orderBy('visitsCount')
                        ->get()
                        ->toArray();
        }

        if ($data->date){
            $date = new Carbon($data->date);

            return DB::table('visits')
            ->selectRaw('SUM(visits.total_hms_bill) as totalHmsBill, SUM(visits.total_hmo_bill) as totalHmoBill, SUM(visits.total_paid) as totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) as discount, sponsors.name as sponsor, sponsors.id as id, sponsors.category_name as category, COUNT(DISTINCT visits.id) as visitsCount, SUM(CASE WHEN visits.hmo_done_by IS NOT NULL THEN 1 ELSE 0 END) AS billsSent, MONTHNAME(visits.created_at) as monthName, YEAR(visits.created_at) as year')
            ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
            ->where('visits.consulted', '!=', null)
            ->where('visits.hmo_done_by', '!=', null)
            ->whereMonth('visits.created_at', $date->month)
            ->whereYear('visits.created_at', $date->year)
            ->where(function (QueryBuilder $query) {
                $query->where('sponsors.category_name', 'HMO')
                ->orWhere('sponsors.category_name', 'NHIS' )
                ->orWhere('sponsors.category_name', 'Retainership' );
            })
            ->groupBy('sponsor', 'id', 'category', 'monthName', 'year')
            ->orderBy('sponsor')
            ->orderBy('visitsCount')
            ->get()
            ->toArray();
        }

        return DB::table('visits')
                        ->selectRaw('SUM(visits.total_hms_bill) as totalHmsBill, SUM(visits.total_hmo_bill) as totalHmoBill, SUM(visits.total_paid) as totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) as discount, sponsors.name as sponsor, sponsors.id as id, sponsors.category_name as category, COUNT(DISTINCT visits.id) as visitsCount, SUM(CASE WHEN visits.hmo_done_by IS NOT NULL THEN 1 ELSE 0 END) AS billsSent, MONTHNAME(visits.created_at) as monthName, YEAR(visits.created_at) as year')
                        ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                        ->where('visits.consulted', '!=', null)
                        ->where('visits.hmo_done_by', '!=', null)
                        ->whereMonth('visits.created_at', $current->month)
                        ->whereYear('visits.created_at', $current->year)
                        ->where(function (QueryBuilder $query) {
                            $query->where('sponsors.category_name', 'HMO')
                            ->orWhere('sponsors.category_name', 'NHIS' )
                            ->orWhere('sponsors.category_name', 'Retainership' );
                        })
                        ->groupBy('sponsor', 'id', 'category', 'monthName', 'year')
                        ->orderBy('sponsor')
                        ->orderBy('visitsCount')
                        ->get()
                        ->toArray();   
    }

    public function getReportSummaryTable1(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'name';
        $orderDir   =  'asc';
        $getDate     = (new Carbon($data->date ?? $data->startDate));
        $month      = $getDate->month;
        $current    = CarbonImmutable::now();

        $visitConstraintsWithoutDate = function (Builder $query) use ($getDate) {
            $query->whereMonth('created_at', $getDate->month)
                  ->whereYear('created_at', $getDate->year)
                  ->whereNotNull('consulted')
                  ->whereNotNull('hmo_done_by');
        };
        
        if (! empty($params->searchTerm)) {
            
            $query = $this->sponsor::with([
                'reminders' => function($query) use($getDate){
                    $query->whereMonth('month_sent_for', $getDate->month)
                    ->whereYear('month_sent_for', $getDate->year);
                },
            ])
            ->withCount([
                'visits as visitsCount' => $visitConstraintsWithoutDate,
                'visits as billsSent' => $visitConstraintsWithoutDate,
            ])
            ->withSum(['visits as totalHmsBill' => $visitConstraintsWithoutDate], 'total_hms_bill')
            ->withSum(['visits as totalHmoBill' => $visitConstraintsWithoutDate], 'total_hmo_bill')
            ->withSum(['visits as nhisBill' => $visitConstraintsWithoutDate], 'total_nhis_bill')
            ->withSum(['visits as totalPaid' => $visitConstraintsWithoutDate], 'total_paid')
            ->withSum(['visits as totalCapitation' => $visitConstraintsWithoutDate], 'total_capitation')
            ->withSum(['visits as discount' => $visitConstraintsWithoutDate], 'discount');

            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            return $query->where(function (Builder $query) use ($searchTerm){
                    $query->where('name', 'LIKE', $searchTerm)
                    ->orWhere('category_name', 'LIKE', $searchTerm);
                })
                ->where(function (Builder $query) {
                    $query->where('category_name', 'HMO')
                    ->orWhere('category_name', 'NHIS' )
                    ->orWhere('category_name', 'Retainership' );
                })
                ->whereHas('visits', $visitConstraintsWithoutDate)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->category){
            if ($data->startDate && $data->endDate){

                $visitConstraintsRange = function (Builder $query) use ($data) {
                    $query->WhereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                          ->whereNotNull('consulted')
                          ->whereNotNull('hmo_done_by');
                };

                $query = $this->sponsor::with([
                    'reminders' => function($query) use($data){
                        $query->whereMonth('month_sent_for', $data->startDate?->month)
                        ->whereYear('month_sent_for', $data->startDate?->year);
                    },
                ])
                ->withCount([
                    'visits as visitsCount' => $visitConstraintsRange,
                    'visits as billsSent' => $visitConstraintsRange,
                ])
                ->withSum(['visits as totalHmsBill' => $visitConstraintsRange], 'total_hms_bill')
                ->withSum(['visits as totalHmoBill' => $visitConstraintsRange], 'total_hmo_bill')
                ->withSum(['visits as nhisBill' => $visitConstraintsRange], 'total_nhis_bill')
                ->withSum(['visits as totalPaid' => $visitConstraintsRange], 'total_paid')
                ->withSum(['visits as totalCapitation' => $visitConstraintsRange], 'total_capitation')
                ->withSum(['visits as discount' => $visitConstraintsRange], 'discount');
    
                return $query->where(function (Builder $query) {
                            $query->where('category_name', 'HMO')
                            ->orWhere('category_name', 'NHIS' )
                            ->orWhere('category_name', 'Retainership' );
                        })
                        ->whereHas('visits', $visitConstraintsRange)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
    
            if ($data->date){
                $date   = new Carbon($data->date);

                $visitConstraints = function (Builder $query) use ($date) {
                    $query->whereMonth('created_at', $date->month)
                          ->whereYear('created_at', $date->year)
                          ->whereNotNull('consulted')
                          ->whereNotNull('hmo_done_by');
                };

                $query  = $this->sponsor::with([
                        'reminders' => function($query) use($date){
                            $query->whereMonth('month_sent_for', $date->month)
                            ->whereYear('month_sent_for', $date->year);
                        },
                    ])
                    ->withCount([
                        'visits as visitsCount' => $visitConstraints,
                        'visits as billsSent' => $visitConstraints,
                    ])
                    ->withSum(['visits as totalHmsBill' => $visitConstraints], 'total_hms_bill')
                    ->withSum(['visits as totalHmoBill' => $visitConstraints], 'total_hmo_bill')
                    ->withSum(['visits as nhisBill' => $visitConstraints], 'total_nhis_bill')
                    ->withSum(['visits as totalPaid' => $visitConstraints], 'total_paid')
                    ->withSum(['visits as totalCapitation' => $visitConstraints], 'total_capitation')
                    ->withSum(['visits as discount' => $visitConstraints], 'discount');
        
                    return $query->where(function (Builder $query) {
                                $query->where('category_name', 'HMO')
                                ->orWhere('category_name', 'NHIS' )
                                ->orWhere('category_name', 'Retainership' );
                            })
                            ->whereHas('visits', $visitConstraints)
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            
            $query      = $this->sponsor::with([
                        'reminders' => function($query) use($getDate){
                            $query->whereMonth('month_sent_for', $getDate->month)
                            ->whereYear('month_sent_for', $getDate->year);
                        },
                    ])
                    ->withCount([
                        'visits as visitsCount' => $visitConstraintsWithoutDate,
                        'visits as billsSent' => $visitConstraintsWithoutDate,
                    ])
                    ->withSum(['visits as totalHmsBill' => $visitConstraintsWithoutDate], 'total_hms_bill')
                    ->withSum(['visits as totalHmoBill' => $visitConstraintsWithoutDate], 'total_hmo_bill')
                    ->withSum(['visits as nhisBill' => $visitConstraintsWithoutDate], 'total_nhis_bill')
                    ->withSum(['visits as totalPaid' => $visitConstraintsWithoutDate], 'total_paid')
                    ->withSum(['visits as totalCapitation' => $visitConstraintsWithoutDate], 'total_capitation')
                    ->withSum(['visits as discount' => $visitConstraintsWithoutDate], 'discount');

            return $query->where('category_name', $data->category)
                        ->whereHas('visits', $visitConstraintsWithoutDate)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->startDate && $data->endDate){

            $startDate = new Carbon($data->startDate);
            $visitConstraintsRange = function (Builder $query) use ($data) {
                $query->WhereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                      ->whereNotNull('consulted')
                      ->whereNotNull('hmo_done_by');
            };

            $query = $this->sponsor::with([
                'reminders' => function($query) use($startDate){
                    $query->whereMonth('month_sent_for', $startDate->month)
                    ->whereYear('month_sent_for', $startDate->year);
                },
            ])
            ->withCount([
                'visits as visitsCount' => $visitConstraintsRange,
                'visits as billsSent' => $visitConstraintsRange,
            ])
            ->withSum(['visits as totalHmsBill' => $visitConstraintsRange], 'total_hms_bill')
            ->withSum(['visits as totalHmoBill' => $visitConstraintsRange], 'total_hmo_bill')
            ->withSum(['visits as nhisBill' => $visitConstraintsRange], 'total_nhis_bill')
            ->withSum(['visits as totalPaid' => $visitConstraintsRange], 'total_paid')
            ->withSum(['visits as totalCapitation' => $visitConstraintsRange], 'total_capitation')
            ->withSum(['visits as discount' => $visitConstraintsRange], 'discount');

            return $query->where(function (Builder $query) {
                        $query->where('category_name', 'HMO')
                        ->orWhere('category_name', 'NHIS' )
                        ->orWhere('category_name', 'Retainership' );
                    })
                    ->whereHas('visits', $visitConstraintsRange)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->date){
            $date = new Carbon($data->date);

            $visitConstraints = function (Builder $query) use ($date) {
                $query->whereMonth('created_at', $date->month)
                      ->whereYear('created_at', $date->year)
                      ->whereNotNull('consulted')
                      ->whereNotNull('hmo_done_by');
            };
           
            $query  = $this->sponsor::with([
                'reminders' => function($query) use($date){
                    $query->whereMonth('month_sent_for', $date->month)
                    ->whereYear('month_sent_for', $date->year);
                },
            ])
            ->withCount([
                'visits as visitsCount' => $visitConstraints,
                'visits as billsSent' => $visitConstraints,
            ])
            ->withSum(['visits as totalHmsBill' => $visitConstraints], 'total_hms_bill')
            ->withSum(['visits as totalHmoBill' => $visitConstraints], 'total_hmo_bill')
            ->withSum(['visits as nhisBill' => $visitConstraints], 'total_nhis_bill')
            ->withSum(['visits as totalPaid' => $visitConstraints], 'total_paid')
            ->withSum(['visits as totalCapitation' => $visitConstraints], 'total_capitation')
            ->withSum(['visits as discount' => $visitConstraints], 'discount');

            return $query->where(function (Builder $query) {
                        $query->where('category_name', 'HMO')
                        ->orWhere('category_name', 'NHIS' )
                        ->orWhere('category_name', 'Retainership' );
                    })
                    ->whereHas('visits', $visitConstraints)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        $query  = $this->sponsor::with([
            'reminders' => function($query) use($getDate){
                $query->whereMonth('month_sent_for', $getDate->month)
                ->whereYear('month_sent_for', $getDate->year);
            },
        ])
        ->withCount([
            'visits as visitsCount' => $visitConstraintsWithoutDate,
            'visits as billsSent' => $visitConstraintsWithoutDate,
        ])
        ->withSum(['visits as totalHmsBill' => $visitConstraintsWithoutDate], 'total_hms_bill')
        ->withSum(['visits as totalHmoBill' => $visitConstraintsWithoutDate], 'total_hmo_bill')
        ->withSum(['visits as nhisBill' => $visitConstraintsWithoutDate], 'total_nhis_bill')
        ->withSum(['visits as totalPaid' => $visitConstraintsWithoutDate], 'total_paid')
        ->withSum(['visits as totalCapitation' => $visitConstraintsWithoutDate], 'total_capitation')
        ->withSum(['visits as discount' => $visitConstraintsWithoutDate], 'discount');

        return $query->where(function (Builder $query) {
                        $query->where('category_name', 'HMO')
                        ->orWhere('category_name', 'NHIS' )
                        ->orWhere('category_name', 'Retainership' );
                    })
                    ->whereHas('visits', $visitConstraintsWithoutDate)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));                        
    }

    public function getReportsSummaryTransformer($data)
    {
        return function (Sponsor $sponsor) use ($data){
            $monthName  = (new Carbon($data->date ?? $data->startDate))->monthName;
            $year       = (new Carbon($data->date ?? $data->startDate))->year;
            $monthYear  = (new Carbon($data->date ?? $data->startDate))->format('F Y');
            return [
                'id'                => $sponsor->id,
                'sponsor'           => $sponsor->name,
                'category'          => $sponsor->category_name,
                'visitsCount'       => $sponsor->visitsCount,
                'billsSent'         => $sponsor->billsSent,
                'totalHmsBill'      => $sponsor->totalHmsBill,
                'totalHmoBill'      => $sponsor->totalHmoBill,
                'nhisBill'          => $sponsor->nhisBill,
                'totalPaid'         => $sponsor->totalPaid,
                'totalCapitation'   => $sponsor->totalCapitation,
                'discount'          => $sponsor->discount,
                'reminderSet'       => $this->updatableReminderDisplay($sponsor->reminders->first()),
                'monthYear'         => $monthYear,
                'monthName'         => $monthName,
                'year'              => $year,
                'flagSponsor'       => $sponsor->flag,
            ];
        };
    }

    public function updatableReminderDisplay($reminder)
    {
        return $reminder ? ($reminder?->confirmed_paid ? '<i class="ms-1 text-primary bi bi-p-circle-fill tooltip-test" title="paid"></i>' . (new Carbon($reminder->confirmed_paid))->format('d/m/y') . ' - ' . number_format((float)$reminder->amount_confirmed) : null) ?? ($reminder?->final_reminder ? $this->reportTableHtmlFormat('Final reminder', $reminder ,'final_reminder') : null ) ?? ($reminder?->second_reminder ? $this->reportTableHtmlFormat('Second reminder', $reminder ,'second_reminder') : null) ?? ($reminder?->first_reminder ? $this->reportTableHtmlFormat('First reminder', $reminder ,'first_reminder') : null) ?? $this->reportTableHtmlFormat('Bill Sent', $reminder, null) : null;
    }

    public function monthYearCoverter($date)
    {
        return (new Carbon($date))->format('F Y');
    }

    public function reportTableHtmlFormat($text, $reminder, $type){
        if ($type){
            return '<span class="confirmedPaidBtn" data-id="' . $reminder->id .'" data-sponsor="'. $reminder->sponsor->name .'" data-monthYear="'. $this->monthYearCoverter($reminder->month_sent_for) .'">'. $text .' - '. $reminder?->$type.'</span>';
        }
        return '<span class="confirmedPaidBtn" data-id="'. $reminder->id .'" data-sponsor="'. $reminder->sponsor->name . '" data-monthYear="' . $this->monthYearCoverter($reminder->month_sent_for) .'">'. $text .'</span>';
    }

    public function getVisitsForReconciliation(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';
        $query = $this->visit::with([
            'sponsor.sponsorCategory', 
            'consultations',
            'patient', 
            'prescriptions' => function ($query) {
                $query->with([
                    'resource.unitDescription',
                    'approvedBy',
                    'rejectedBy',
                    'visit.sponsor'

                ]);
            },
            'doctor', 
        ])
        ->where('sponsor_id', $data->sponsorId)
        ->whereNotNull('consulted');

            if (! empty($params->searchTerm)) {
                $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
                if ($data->from && $data->to){
                    return $query->where(function (Builder $query) use($searchTerm) {
                                $query->whereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                                ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm )
                                ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                                ->orWhereRelation('user', 'username', 'LIKE', $searchTerm)
                                ->orWhereRelation('prescriptions.hmoBillBy', 'username', 'LIKE', $searchTerm)
                                ->orWhereRelation('prescriptions.resource', 'name', 'LIKE', $searchTerm);
                            })
                            ->WhereBetween('created_at', [$data->from.' 00:00:00', $data->to.' 23:59:59'])
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
                }

                if ($data->date){
                    $date = new Carbon($data->date);
                    return $query->where(function (Builder $query) use($searchTerm) {
                        $query->whereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('user', 'username', 'LIKE', $searchTerm)
                        ->orWhereRelation('prescriptions.hmoBillBy', 'username', 'LIKE', $searchTerm)
                        ->orWhereRelation('prescriptions.resource', 'name', 'LIKE', $searchTerm);
                    })
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
                }

                return $query->where(function (Builder $query) use($searchTerm) {
                                $query->whereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                                ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                                ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                                ->orWhereRelation('user', 'username', 'LIKE', $searchTerm)
                                ->orWhereRelation('prescriptions.hmoBillBy', 'username', 'LIKE', $searchTerm)
                                ->orWhereRelation('prescriptions.resource', 'name', 'LIKE', $searchTerm);
                            })
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if ($data->from && $data->to){
                return $query->WhereBetween('created_at', [$data->from.' 00:00:00', $data->to.' 23:59:59'])
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if ($data->date){
                $date = new Carbon($data->date);

                return $query->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

        return $query->where('consulted', '!=', null)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getVisitsForReconciliationTransformer(): callable
    {
       return  function (Visit $visit) {
            $latestConsultation = $visit->consultations->sortDesc()->first();
            $visit->update(['total_capitation' => $visit->totalPrescriptionCapitations()]);
            return [
                'id'                    => $visit->id,
                'came'                  => (new Carbon($visit->created_at))->format('D d/m/y g:ia'),                
                'patient'               => $visit->patient->patientId(),
                'consultBy'             => $visit->doctor->username,
                'diagnosis'             => $latestConsultation?->icd11_diagnosis ?? $latestConsultation?->provisional_diagnosis ?? $latestConsultation?->assessment, 
                'sponsorCategory'       => $visit->sponsor->category_name,
                'sponsorCategoryClass'  => $visit->sponsor->sponsorCategory->pay_class,
                'closed'                => $visit->closed,
                'totalHmsBill'          => $visit->total_hms_bill,
                'totalHmoBill'          => $visit->total_hmo_bill,
                'totalNhisBill'         => $visit->total_nhis_bill,
                'totalCapitation'       => $visit->total_capitation,
                'totalPaid'             => $visit->total_paid,
                'prescriptions'         => $visit->prescriptions->map(fn(Prescription $prescription)=> [
                    'id'                => $prescription->id ?? '',
                    'prescribed'        => (new Carbon($prescription->created_at))->format('D d/m/y g:ia') ?? '',
                    'item'              => $prescription->resource->name,
                    'prescription'      => $prescription->prescription ?? '',
                    'qtyBilled'         => $prescription->qty_billed,
                    'unit'              => $prescription->resource->unitDescription?->short_name,
                    'hmoBill'           => $prescription->hmo_bill ?? '',
                    'hmsBill'           => $prescription->hms_bill ?? '',
                    'nhisBill'          => $prescription->nhis_bill ?? '',
                    'capitation'        => $prescription->capitation ?? '',
                    'approved'          => $prescription->approved, 
                    'rejected'          => $prescription->rejected,
                    'hmoNote'           => $prescription->hmo_note ?? '',
                    'statusBy'          => $prescription->approvedBy?->username ?? $prescription->rejectedBy?->username ?? '',
                    'note'              => $prescription->note ?? '',
                    'status'            => $prescription->status ?? '',
                    'paidNhis'          => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill/10 && $prescription->visit->sponsor->category_name == 'NHIS',
                    'paid'              => $prescription->paid ?? '',
                ]),
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient?->flag_reason,
            ];
         };
    }

    public function savePayment(Request $data, Prescription $prescription, User $user)
    {
        return DB::transaction(function () use($data, $prescription, $user) {

            $prescription->update([
                'paid'      => $data->amountPaid ?? 0,
                'paid_by'   => $user->id
            ]);

            $visit = $prescription->visit;

            $visit->total_paid = $visit->totalPaidPrescriptions();
            $visit->save();

            return $prescription;
        });
    }

    public function saveBulkPayment(Request $data, Visit $visit)
    {
        return DB::transaction(function () use($data, $visit) {
            $prescriptions  = $visit->prescriptions;

            if ($visit->sponsor->category_name == 'Retainership'){
                $this->paymentService->prescriptionsPaymentSeiveRetanership((float)$data->bulkPayment, $prescriptions);
            } else {
                $this->paymentService->prescriptionsPaymentSeiveHmo((float)$data->bulkPayment, $prescriptions);
            }
                

            $visit->total_paid = $visit->totalPaidPrescriptions();
            $visit->save();

            return $visit;
        });
    }

    public function determineValueOfTotalPaid(Visit $visit)
    {
        return $visit->totalPaidPrescriptions();
    }

    public function getNhisSponsorsByDate(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'name';
        $orderDir   =  'asc';
        $current    = CarbonImmutable::now();
        $searchDate = $data->date ? (new Carbon($data->date)) : null;

        if (! empty($params->searchTerm)) {
            if ($searchDate){
                return $this->sponsor
                        ->where('category_name', 'NHIS')
                        ->whereHas('visits', function(Builder $query) use($searchDate){
                            $query->whereMonth('created_at', $searchDate->month)
                                    ->whereYear('created_at', $searchDate->year);
                        })
                        ->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            return $this->sponsor
                        ->where('category_name', 'NHIS')
                        ->whereHas('visits', function(Builder $query) use($current){
                            $query->whereMonth('created_at', $current->month)
                                  ->whereYear('created_at', $current->year);
                        })
                        ->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($searchDate){
            return $this->sponsor
                    ->where('category_name', 'NHIS')
                    ->whereHas('visits', function(Builder $query) use($searchDate){
                        $query->whereMonth('created_at', $searchDate->month)
                                ->whereYear('created_at', $searchDate->year);
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
        return $this->sponsor
                    ->where('category_name', 'NHIS')
                    ->whereHas('visits', function(Builder $query) use($current){
                        $query->whereMonth('created_at', $current->month)
                              ->whereYear('created_at', $current->year);
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));                        
    }

    public function getSponsorsByDateTransformer($data)
    {
        return function (Sponsor $sponsor) use ($data){
            $month      = (new Carbon($data->date))->month;
            $year       = (new Carbon($data->date))->year;
            $monthYear  = (new Carbon($data->date))->format('F Y');
            return [
                'id'                => $sponsor->id,
                'sponsor'           => $sponsor->name,
                'category'          => $sponsor->category_name,
                'patientsR'         => $sponsor->patients->count(),
                'patientsC'         => $sponsor->patients()->whereHas('visits', fn(Builder $query)=>$query->whereMonth('created_at', $month))->count(),
                'visitsC'           => $sponsor->visits()->whereMonth('created_at', $month)->count(),
                'visitsP'           => $sponsor->visits()->whereHas('prescriptions', fn(Builder $query)=>$query->whereMonth('created_at', $month))->count(),
                'prescriptions'     => $sponsor->through('visits')->has('prescriptions')->whereMonth('prescriptions.created_at', $month)->count(),
                'hmsBill'           => $sponsor->visits()->whereMonth('created_at', $month)->sum('total_hms_bill'),
                'nhisBill'          => $sponsor->visits()->whereMonth('created_at', $month)->sum('total_nhis_bill'),
                'paid'              => $sponsor->visits()->whereMonth('created_at', $month)->sum('total_paid'),
                'capitationPayment' => $sponsor->capitationPayments()->whereMonth('month_paid_for', $month)->whereYear('month_paid_for', $year)->first()?->amount_paid,
                'monthYear'         => $monthYear,
            ];
        };
    }

    public function totalYearlyIncomeFromHmoPatients($data)
    {
        $currentDate = new Carbon();
        if ($data->year){

            return DB::table('reminders')
                            ->selectRaw('SUM(amount_confirmed) as paidHmo, MONTH(created_at) as month, MONTHNAME(created_at) as month_name')
                            ->whereNull('visit_id')
                            ->whereYear('created_at', $data->year)
                            ->groupBy('month_name', 'month')
                            ->orderBy('month')
                            ->get();
        }

        return DB::table('reminders')
                        ->selectRaw('SUM(amount_confirmed) as paidHmo, MONTH(created_at) as month, MONTHNAME(created_at) as month_name')
                        ->whereNull('visit_id')
                        ->whereYear('created_at', $currentDate->year)
                        ->groupBy('month_name', 'month')
                        ->orderBy('month')
                        ->get();
    }
}