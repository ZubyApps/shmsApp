<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Consultation;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class HmoService
{
    public function __construct(
        private readonly Visit $visit, 
        private readonly Prescription $prescription,
        private readonly PayPercentageService $payPercentageService
        )
    {
        
    }

    public function getPaginatedVerificationList(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->visit
                        ->Where('verified_at', null)
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
                'doctor'            => $visit->doctor->username ?? '',
                'codeText'          => $visit->verification_code,
                'phone'             => $visit->patient->phone,
                'status'            => $visit->verification_status,
                '30dayCount'        => $visit->patient->visits->where('consulted', '>', (new Carbon())->subDays(30))->count().' visit(s)',
            ];
         };
    }

    public function verify(Request $request, Visit $visit): Visit
    {
       
            $visit->update([
                'verification_status'   => $request->status,
                'verification_code'     => $request->codeText,
                'verified_at'           => $request->status === 'Verified' ? new Carbon() : null
            ]);

            return $visit;
    }

    public function getPaginatedAllConsultedHmoVisits(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->where(function (Builder $query) use($params) {
                        $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('consultations', 'icd11_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('consultations', 'admission_status', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                    })
                    
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Outpatient'){
            return $this->visit
            ->where('consulted', '!=', null)
            ->where('closed', null)
            ->where('hmo_done_by', null)
            ->whereRelation('consultations', 'admission_status', '=', 'Outpatient')
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
                    ->where('closed', null)
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->where(function (Builder $query) {
                        $query->whereRelation('consultations', 'admission_status', '=', 'Inpatient')
                        ->orWhereRelation('consultations', 'admission_status', '=', 'Observation');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
        if ($data->filterBy == 'ANC'){
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('hmo_done_by', null)
                    ->where('closed', null)
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
                    ->where('closed', null)
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
                'doctor'            => $visit->doctor->username,
                'diagnosis'         => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->icd11_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->provisional_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->assessment,
                'sponsor'           => $visit->sponsor->name,
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
                'sponsorCategory'   => $visit->sponsor->sponsorCategory->name,
                'payPercent'        => $this->payPercentageService->individual_Family($visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                '30dayCount'        => $visit->patient->visits->where('consulted', '>', (new Carbon())->subDays(30))->count().' visit(s)',

            ];
         };
    }

    public function getPaginatedAllPrescriptionsRequest(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

            if (! empty($params->searchTerm)) {
                return $this->prescription
                            // ->where('rejected', false)
                            ->where(function (Builder $query) {
                                $query->whereRelation('visit.sponsor.sponsorCategory', 'name', 'HMO')
                                ->orWhereRelation('visit.sponsor.sponsorCategory', 'name', 'NHIS')
                                ->orWhereRelation('visit.sponsor.sponsorCategory', 'name', 'Retainership');
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

        return $this->prescription
                ->where('approved', false)
                ->where('rejected', false)
                ->where(function (Builder $query) {
                    $query->whereRelation('visit.sponsor.sponsorCategory', 'name', 'HMO')
                    ->orWhereRelation('visit.sponsor.sponsorCategory', 'name', 'NHIS')
                    ->orWhereRelation('visit.sponsor.sponsorCategory', 'name', 'Retainership')
                    ;
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
                'doctor'            => $prescription->user->username,
                'prescribed'        => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                'diagnosis'         => $prescription->consultation?->icd11_diagnosis ?? 
                                       $prescription->consultation?->provisional_diagnosis ?? 
                                       $prescription->consultation?->assessment, 
                'resource'          => $prescription->resource->name,
                'prescription'      => $prescription->prescription,
                'quantity'          => $prescription->qty_billed,
                'note'              => $prescription->note,
                'hmsBill'           => $prescription->hms_bill ?? '',
                'hmsBillDate'       => $prescription->hms_bill_date ? (new Carbon($prescription->hms_bill_date))->format('d/m/y g:ia') : '',
                'hmoBill'           => $prescription->hmo_bill,
                'hmoBillBy'         => $prescription->hmoBillBy?->username,
                'paidHms'           => $prescription->visit->totalPayments() ?? '',
                'approved'          => $prescription->approved,
                'approvedBy'        => $prescription->approvedBy?->username,
                'rejected'          => $prescription->rejected,
                'rejectedBy'        => $prescription->rejectedBy?->username,
            ];
         };
    }

    public function approve($data, Prescription $prescription, User $user)
    {
        if ($prescription->approved == true || $prescription->rejected == true){
            return response('Already treated by ' . $prescription->approvedBy->username ?? $prescription->rejectedBy->username, 222);
        }
        return  $prescription->update([
            'approved'         => true,
            'hmo_note'          => $data->note,
            'approved_by'      => $user->id,
        ]);
    }

    public function reject($data, Prescription $prescription, User $user)
    {
        if ($prescription->approved == true || $prescription->rejected == true){
            return response('Already treated by ' . $prescription->rejectedBy->username ??  $prescription->approvedBy->username, 222);
        }
        return  $prescription->update([
            'rejected'          => true,
            'hmo_note'          => $data->note,
            'rejected_by'       => $user->id,
        ]);
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
                                ->orWhereRelation('resource.rescurceSubCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('resource.rescurceSubCategory.resourceCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
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
            'hmo_bill'       => $data->bill,
            'hmo_bill_date'  => new Carbon(),
            'hmo_bill_by'    => $user->id,
            'hmo_bill_note'  => $data->note
        ]);   
        
        return $prescription;
    }
}