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
        private readonly Prescription $prescription
        )
    {
        
    }

    public function getPaginatedVerificationList(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->visit
                        ->Where('verification_status', null)
                        ->where(function (Builder $query) use($params) {
                            $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('verification_status', false)
                    ->where(function (Builder $query) use($params) {
                        $query->whereRelation('sponsor.sponsorCategory', 'name', '=', 'HMO')
                        ->orWhereRelation('sponsor.sponsorCategory', 'name', '=', 'NHIS');
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
                'phone'            => $visit->patient->phone,
            ];
         };
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
            ->whereRelation('consultations', 'admission_status', '=', 'Outpatient')
            ->whereRelation('patient', 'patient_type', '!=', 'ANC')
            ->where(function (Builder $query) {
                $query->whereRelation('sponsor', 'category_name', 'HMO')
                ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                ->orWhereRelation('sponsor', 'category_name', 'Retainership');
            })
            ->where('closed', false)
            ->orderBy($orderBy, $orderDir)
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Inpatient'){
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->where(function (Builder $query) {
                        $query->whereRelation('consultations', 'admission_status', '=', 'Inpatient')
                        ->orWhereRelation('consultations', 'admission_status', '=', 'Observation');
                    })
                    ->where('closed', false)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
        if ($data->filterBy == 'ANC'){
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->whereRelation('patient', 'patient_type', '=', 'ANC')
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->where('closed', false)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('consulted', '!=', null)
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->where('closed', false)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getPaginatedAllPrescriptionsRequest(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

            if (! empty($params->searchTerm)) {
                return $this->prescription
                            ->where('approved', false)
                            ->where(function (Builder $query) use($params) {
                                $query->whereRelation('visit.sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('resource.rescurceSubCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('resource.rescurceSubCategory.resourceCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                            })
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

        return $this->prescription
                ->where('approved', false)
                ->where('rejected', false)
                ->where(function (Builder $query) {
                    $query->whereRelation('visit.sponsor.sponsorCategory', 'name', 'HMO')
                    ->orWhereRelation('visit.sponsor.sponsorCategory', 'name', 'NHIS');
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

            ];
         };
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
                'diagnosis'         => $prescription->consultation?->icd11_diagnosis,
                'resource'          => $prescription->resource->name,
                'prescription'      => $prescription->prescription,
                'quantity'          => $prescription->qty_billed,
                'hmsBill'           => $prescription->hms_bill,
                'hmsBillDate'       => $prescription->hms_bill_date ? (new Carbon($prescription->hms_bill_date))->format('d/m/y g:ia') : '',
                'approved_status'   => $prescription->approved,
            ];
         };
    }

    public function approve($data, Prescription $prescription, User $user)
    {
        return  $prescription->update([
            'approved'         => true,
            'approval_note'    => $data->note,
            'approved_by'      => $user->id,
        ]);
    }

    public function reject($data, Prescription $prescription, User $user)
    {
        return  $prescription->update([
            'rejected'          => true,
            'rejection_note'    => $data->note,
            'rejected_by'       => $user->id,
        ]);
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