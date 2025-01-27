<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Consultation;
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

        if (! empty($params->searchTerm)) {
            return $this->visit
                        ->where(function (Builder $query) use($params) {
                            $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('verified_at', null)
                    ->where(function (Builder $query) use($params) {
                        $query->whereRelation('sponsor.sponsorCategory', 'name', '=', 'HMO')
                        ->orWhereRelation('sponsor.sponsorCategory', 'name', '=', 'NHIS')
                        ->orWhereRelation('sponsor.sponsorCategory', 'name', '=', 'Retainership');
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
            ];
         };
    }

    public function verify(Request $request, Visit $visit): Visit
    {
       
            $visit->update([
                'verification_status'   => $request->status,
                'verification_code'     => $request->codeText,
                'verified_at'           => $request->status === 'Verified' ? new Carbon() : null,
                'verified_by'           => $request->user()->id,
            ]);

            return $visit;
    }

    public function getPaginatedAllConsultedHmoVisits(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'consulted';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where(function (Builder $query) use($params) {
                        $query->where('created_at', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('consultations', 'icd11_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('consultations', 'admission_status', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                    })
                    
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Outpatient'){
            return $this->visit
            ->where('consulted', '!=', null)
            ->where('closed', false)
            ->where('hmo_done_by', null)
            ->where('admission_status', '=', 'Outpatient')
            ->whereRelation('patient', 'patient_type', '!=', 'ANC')
            ->where(function (Builder $query) {
                $query->whereRelation('sponsor', 'category_name', 'HMO')
                ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                ->orWhereRelation('sponsor', 'category_name', 'Retainership');
            })
            ->orderBy($orderBy, $orderDir)
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Inpatient'){
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('hmo_done_by', null)
                    ->where('closed', false)
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
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('hmo_done_by', null)
                    ->where('closed', false)
                    ->whereRelation('patient', 'patient_type', '=', 'ANC')
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('hmo_done_by', null)
                    ->where('closed', false)
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
                'diagnosis'         => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->icd11_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->provisional_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->assessment,
                'sponsor'           => $visit->sponsor->name,
                'sponsorCategory'   => $visit->sponsor->category_name,
                'flagSponsor'       => $visit->sponsor->flag,
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient?->flag_reason,
                'vitalSigns'        => $visit->vitalSigns->count(),
                'admissionStatus'   => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->admission_status,
                'patientType'       => $visit->patient->patient_type,
                'labPrescribed'     => Prescription::where('visit_id', $visit->id)
                                        ->whereRelation('resource.resourceSubCategory.resourceCategory', 'name', '=', 'Investigations')
                                        ->count(),
                'labDone'           => Prescription::where('visit_id', $visit->id)
                                        ->whereRelation('resource.resourceSubCategory.resourceCategory', 'name', '=', 'Investigations')
                                        ->where('result_date','!=', null)
                                        ->count(),
                'payPercent'        => $this->payPercentageService->individual_Family($visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'thirtyDayCount'    => explode(".", $visit->patient->patient_type)[0] == 'ANC' ? $visit->consultations->count() : $visit->patient->visits->where('consulted', '>', (new Carbon())->subDays(30))->count().' visit(s)',
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

            if (! empty($params->searchTerm)) {
                return $this->prescription
                    ->where(function (Builder $query) use($data) {
                        $query->whereRelation('visit.sponsor.sponsorCategory', 'name', ($data->sponsor == 'NHIS' ? '' : 'HMO'))
                        ->orWhereRelation('visit.sponsor.sponsorCategory', 'name', ($data->sponsor == 'NHIS' ? 'NHIS' : ''))
                        ->orWhereRelation('visit.sponsor.sponsorCategory', 'name', ($data->sponsor == 'NHIS' ? '' : 'Retainership'));
                    })
                    ->where(function (Builder $query) use($params) {
                        $query->whereRelation('visit.sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('resource.resourceSubCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('resource.resourceSubCategory.resourceCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('approvedBy', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%')
                        ->orWhereRelation('rejectedBy', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if ($data->sponsor == 'NHIS'){
                return $this->prescription
                    ->where('approved', false)
                    ->where('rejected', false)
                    ->whereRelation('visit.sponsor.sponsorCategory', 'name', 'NHIS')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $this->prescription
                    ->where('approved', false)
                    ->where('rejected', false)
                    ->where(function (Builder $query) {
                        $query->whereRelation('visit.sponsor.sponsorCategory', 'name', 'HMO')
                        ->orWhereRelation('visit.sponsor.sponsorCategory', 'name', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getAllPrescriptionsTransformer(): callable
    {
       return  function (Prescription $prescription) {
            return [
                'id'                => $prescription->id,
                'patient'           => $prescription->visit->patient->patientId(),
                'sponsor'           => $prescription->visit->sponsor->name,
                'sponsorCategory'   => $prescription->visit->sponsor->category_name,
                'doctor'            => $prescription->user->username,
                'prescribed'        => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                'diagnosis'         => $prescription->consultation?->icd11_diagnosis ?? 
                                       $prescription->consultation?->provisional_diagnosis ?? 
                                       $prescription->consultation?->assessment, 
                'resource'          => $prescription->resource->name,
                'prescription'      => $prescription->prescription,
                'quantity'          => $prescription->qty_billed,
                'totalQuantity'     => $prescription->resource->prescriptions()->where('visit_id', $prescription->visit->id)->sum('qty_billed'),
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
                $this->paymentService->prescriptionsPaymentSeiveNhis($visit->totalPayments(), $visit->prescriptions);
            } else {
                $this->paymentService->prescriptionsPaymentSeive($visit->totalPayments(), $visit->prescriptions);
            }

            return $visit->update([
                'total_nhis_bill'   => $visit->sponsor->category_name == 'NHIS' ? $visit->totalNhisBills() : 0
            ]);
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
                $this->paymentService->prescriptionsPaymentSeiveNhis($visit->totalPayments(), $visit->prescriptions);
            } else {
                $this->paymentService->prescriptionsPaymentSeive($visit->totalPayments(), $visit->prescriptions);
            }

            return $visit->update([
                'total_hms_bill'    => $prescription->visit->totalHmsBills(),
                'total_nhis_bill'   => $prescription->visit->sponsor->category_name == 'NHIS' ? $prescription->visit->totalNhisBills() : 0,
            ]);
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
                'hmo_note'          => null,
                'rejected_by'       => null,
            ]);

            $visit    = $prescription->visit;

            $isNhis = $visit->sponsor->category_name == 'NHIS';

            if ($isNhis){
                $this->paymentService->prescriptionsPaymentSeiveNhis($visit->totalPayments(), $visit->prescriptions);
            } else {
                $this->paymentService->prescriptionsPaymentSeive($visit->totalPayments(), $visit->prescriptions);
            }
    
            return $visit->update([
                'total_hms_bill'    => $prescription->visit->totalHmsBills(),
                'total_nhis_bill'   => $prescription->visit->sponsor->category_name == 'NHIS' ? $prescription->visit->totalNhisBills() : 0,
            ]);
        });

    }

    public function getPaginatedVisitPrescriptionsRequest(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

            if (! empty($params->searchTerm)) {
                return $this->prescription
                            ->where('visit_id', $data->visitId)
                            ->where(function (Builder $query) use($params) {
                                $query->whereRelation('consultation', 'icd11_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('resource.resourceSubCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('resource.resourceSubCategory.resourceCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                            })
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

        return $this->prescription
                ->where('visit_id', $data->visitId)
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

        if (! empty($params->searchTerm)) {
            if ($data->startDate && $data->endDate){
                return $this->visit
                        ->Where('hmo_done_by', '!=', null)
                        ->where(function (Builder $query) use($params) {
                            $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('sponsor.sponsorCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('hmoDoneBy', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if ($data->date){
                $date = new Carbon($data->date);

                return $this->visit
                        ->Where('hmo_done_by', '!=', null)
                        ->where(function (Builder $query) use($params) {
                            $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('sponsor.sponsorCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('hmoDoneBy', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            return $this->visit
                        ->Where('hmo_done_by', '!=', null)
                        ->where(function (Builder $query) use($params) {
                            $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('sponsor.sponsorCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('hmoDoneBy', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->startDate && $data->endDate){

            if ($data->filterByOpen){
                return $this->visit
                        ->where('consulted', '!=', null)
                        ->where('hmo_done_by', '!=', null)
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

            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('hmo_done_by', '!=', null)
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
                return $this->visit
                        ->where('consulted', '!=', null)
                        ->where('hmo_done_by', '!=', null)
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

            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('hmo_done_by', '!=', null)
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
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('hmo_done_by', '!=', null)
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

        return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('hmo_done_by', '!=', null)
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
            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patient'           => $visit->patient->patientId(),
                'sponsor'           => $visit->sponsor->name,
                'doctor'            => $visit->doctor->username,
                'diagnosis'         => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->icd11_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->provisional_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->assessment,
                'sentBy'            => $visit->hmoDoneBy?->username,
                'totalHmsBill'      => $visit->total_hms_bill,
                'totalHmoBill'      => $visit->total_hmo_bill,
                'sponsorCategory'   => $visit->sponsor->sponsorCategory->name,
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'closed'            => $visit->closed,
                'flagSponsor'       => $visit->sponsor->flag,
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient?->flag_reason,
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
        $current    = CarbonImmutable::now();
        
        if (! empty($params->searchTerm)) {
            return $this->sponsor
                ->where(function (Builder $query) use ($params){
                    $query->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                    ->orWhere('category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                })
                ->where(function (Builder $query) {
                    $query->where('category_name', 'HMO')
                    ->orWhere('category_name', 'NHIS' )
                    ->orWhere('category_name', 'Retainership' );
                })
                ->whereHas('visits', function(Builder $query) use($current){
                    $query->where('consulted', '!=', null)
                        ->where('hmo_done_by', '!=', null);
                })
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->category){
            if ($data->startDate && $data->endDate){
                return $this->sponsor
                        ->where('category_name', $data->category)
                        ->whereHas('visits', function(Builder $query) use($data){
                            $query->WhereBetween('visits.created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                                  ->where('consulted', '!=', null)
                                  ->where('hmo_done_by', '!=', null);
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
    
            if ($data->date){
                $date = new Carbon($data->date);
    
                return $this->sponsor
                        ->where('category_name', $data->category)
                        ->whereHas('visits', function(Builder $query) use($date){
                            $query->whereMonth('created_at', $date->month)
                                  ->whereYear('created_at', $date->year)
                                  ->where('consulted', '!=', null)
                                  ->where('hmo_done_by', '!=', null);
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            return $this->sponsor
                        ->where('category_name', $data->category)
                        ->whereHas('visits', function(Builder $query) use($current){
                            $query->whereMonth('created_at', $current->month)
                                  ->whereYear('created_at', $current->year)
                                  ->where('consulted', '!=', null)
                                  ->where('hmo_done_by', '!=', null);
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->startDate && $data->endDate){
            return $this->sponsor
                    ->where(function (Builder $query) {
                        $query->where('category_name', 'HMO')
                        ->orWhere('category_name', 'NHIS' )
                        ->orWhere('category_name', 'Retainership' );
                    })
                    ->whereHas('visits', function(Builder $query) use($data){
                        $query->WhereBetween('visits.created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                              ->where('consulted', '!=', null)
                              ->where('hmo_done_by', '!=', null);
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->date){
            $date = new Carbon($data->date);

            return $this->sponsor
                    ->where(function (Builder $query) {
                        $query->where('category_name', 'HMO')
                        ->orWhere('category_name', 'NHIS' )
                        ->orWhere('category_name', 'Retainership' );
                    })
                    ->whereHas('visits', function(Builder $query) use($date){
                        $query->whereMonth('created_at', $date->month)
                              ->whereYear('created_at', $date->year)
                              ->where('consulted', '!=', null)
                              ->where('hmo_done_by', '!=', null);
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
        return $this->sponsor
                    ->where(function (Builder $query) {
                        $query->where('category_name', 'HMO')
                        ->orWhere('category_name', 'NHIS' )
                        ->orWhere('category_name', 'Retainership' );
                    })
                    ->whereHas('visits', function(Builder $query) use($current){
                        $query->whereMonth('created_at', $current->month)
                              ->whereYear('created_at', $current->year)
                              ->where('consulted', '!=', null)
                              ->where('hmo_done_by', '!=', null);
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));                        
    }

    public function getReportsSummaryTransformer($data)
    {
        return function (Sponsor $sponsor) use ($data){
            $month      = (new Carbon($data->date))->month;
            $monthName  = (new Carbon($data->date))->monthName;
            $year       = (new Carbon($data->date))->year;
            $monthYear  = (new Carbon($data->date))->format('F Y');
            $reminder  = $sponsor->reminders()->whereMonth('month_sent_for', $month)->whereYear('month_sent_for', $year)->first();
            return [
                'id'                => $sponsor->id,
                'sponsor'           => $sponsor->name,
                'category'          => $sponsor->category_name,
                'patientsR'         => $sponsor->patients->count(),
                'patientsC'         => $sponsor->patients()->whereHas('visits', fn(Builder $query)=>$query->whereMonth('created_at', $month))->count(),
                'visitsCount'       => $sponsor->visits()->whereMonth('created_at', $month)->count(),
                'billsSent'         => $sponsor->visits()->whereMonth('created_at', $month)->whereNotNull('hmo_done_by')->count(),
                'visitsP'           => $sponsor->visits()->whereHas('prescriptions', fn(Builder $query)=>$query->whereMonth('created_at', $month))->count(),
                'prescriptions'     => $sponsor->through('visits')->has('prescriptions')->whereMonth('prescriptions.created_at', $month)->count(),
                'totalHmsBill'      => $sponsor->visits()->whereMonth('created_at', $month)->sum('total_hms_bill'),
                'totalHmoBill'      => $sponsor->visits()->whereMonth('created_at', $month)->sum('total_hmo_bill'),
                'nhisBill'          => $sponsor->visits()->whereMonth('created_at', $month)->sum('total_nhis_bill'),
                'totalPaid'         => $sponsor->visits()->whereMonth('created_at', $month)->sum('total_paid'),
                'totalCapitation'   => $sponsor->visits()->whereMonth('created_at', $month)->sum('total_capitation'),
                'discount'          => $sponsor->visits()->whereMonth('created_at', $month)->sum('discount'),
                'reminderSet'       => $this->updatableReminderDisplay($reminder),
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

            if (! empty($params->searchTerm)) {

                if ($data->from && $data->to){
                    return $this->visit
                            ->where('sponsor_id', $data->sponsorId)
                            ->where('consulted', '!=', null)
                            ->where(function (Builder $query) use($params) {
                                $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescriptions.hmoBillBy', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescriptions.resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                            })
                            ->WhereBetween('created_at', [$data->from.' 00:00:00', $data->to.' 23:59:59'])
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
                }

                if ($data->date){
                    $date = new Carbon($data->date);
                    return $this->visit
                    ->where('sponsor_id', $data->sponsorId)
                    ->where('consulted', '!=', null)
                    ->where(function (Builder $query) use($params) {
                        $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('prescriptions.hmoBillBy', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('prescriptions.resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                    })
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
                }

                return $this->visit
                            ->where('sponsor_id', $data->sponsorId)
                            ->where('consulted', '!=', null)
                            ->where(function (Builder $query) use($params) {
                                $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescriptions.hmoBillBy', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescriptions.resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                            })
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if ($data->from && $data->to){
                return $this->visit
                        ->where('sponsor_id', $data->sponsorId)
                        ->where('consulted', '!=', null)
                        ->WhereBetween('created_at', [$data->from.' 00:00:00', $data->to.' 23:59:59'])
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if ($data->date){
                $date = new Carbon($data->date);

                return $this->visit
                        ->where('sponsor_id', $data->sponsorId)
                        ->where('consulted', '!=', null)
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

        return $this->visit
                ->where('sponsor_id', $data->sponsorId)
                ->where('consulted', '!=', null)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getVisitsForReconciliationTransformer(): callable
    {
       return  function (Visit $visit) {
            $visit->update(['total_capitation' => $visit->totalPrescriptionCapitations()]);
            return [
                'id'                    => $visit->id,
                'came'                  => (new Carbon($visit->created_at))->format('D d/m/y g:ia'),                
                'patient'               => $visit->patient->patientId(),
                'consultBy'             => $visit->doctor->username,
                'diagnosis'             => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->icd11_diagnosis ?? 
                                           Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->provisional_diagnosis ?? 
                                           Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->assessment, 
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
                    'unit'              => $prescription->resource->unit_description,
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
                    'paidNhis'          => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill/10 && $prescription->visit->sponsor->sponsorCategory->name == 'NHIS',
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

            $this->paymentService->prescriptionsPaymentSeiveHmo((float)$data->bulkPayment, $prescriptions);

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
                        ->whereRelation('sponsorCategory', 'name', '=', 'NHIS')
                        ->whereHas('visits', function(Builder $query) use($searchDate){
                            $query->whereMonth('created_at', $searchDate->month)
                                    ->whereYear('created_at', $searchDate->year);
                        })
                        ->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            return $this->sponsor
                        ->whereRelation('sponsorCategory', 'name', '=', 'NHIS')
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
                    ->whereRelation('sponsorCategory', 'name', '=', 'NHIS')
                    ->whereHas('visits', function(Builder $query) use($searchDate){
                        $query->whereMonth('created_at', $searchDate->month)
                                ->whereYear('created_at', $searchDate->year);
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
        return $this->sponsor
                    ->whereRelation('sponsorCategory', 'name', '=', 'NHIS')
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