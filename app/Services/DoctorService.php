<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Http\Resources\PatientBioResource;
use App\Models\Consultation;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DoctorService
{
    public function __construct(private readonly Visit $visit)
    {
        
    }

    public function getPaginatedOutpatientConsultedVisits($data, DataTableQueryParams $params, User $user)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where(function (Builder $query) use($params) {
                        $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('consultations', 'icd11_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('consultations', 'admission_status', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                    })
                    
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'My Patients'){
            return $this->visit
            ->where('consulted', '!=', null)
            ->where('user_id', '=', $user->id)
            ->where('doctor_done_by', null)
            ->where('closed', null)
            ->whereRelation('consultations', 'admission_status', '=', 'Outpatient')
            ->whereRelation('patient', 'patient_type', '!=', 'ANC')
            ->orderBy($orderBy, $orderDir)
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        // if ($data->filterBy == 'Inpatient'){
        //     return $this->visit
        //             ->where('consulted', '!=', null)
        //             ->where('user_id', '=', $user->id)
        //             ->where('doctor_done_by', null)
        //             ->where('closed', null)
        //             ->where(function (Builder $query) {
        //                 $query->whereRelation('consultations', 'admission_status', '=', 'Inpatient')
        //                 ->orWhereRelation('consultations', 'admission_status', '=', 'Observation');
        //             })
        //             ->orderBy($orderBy, $orderDir)
        //             ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        // }

        return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('doctor_done_by', null)
                    ->where('closed', null)
                    ->whereRelation('consultations', 'admission_status', '=', 'Outpatient')
                    ->whereRelation('patient', 'patient_type', '!=', 'ANC')
                    ->where('user_id', '=', $user->id)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getPaginatedInpatientConsultedVisits($data, DataTableQueryParams $params, User $user)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where(function (Builder $query) use($params) {
                        $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('consultations', 'icd11_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('consultations', 'admission_status', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                    })
                    
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'My Patients'){
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('doctor_done_by', null)
                    ->where('closed', null)
                    ->where('user_id', '=', $user->id)
                    ->where(function (Builder $query) {
                        $query->whereRelation('consultations', 'admission_status', '=', 'Inpatient')
                        ->orWhereRelation('consultations', 'admission_status', '=', 'Observation');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('doctor_done_by', null)
                    ->where('closed', null)
                    ->whereRelation('patient', 'patient_type', '!=', 'ANC')
                    ->where(function (Builder $query) {
                        $query->whereRelation('consultations', 'admission_status', '=', 'Inpatient')
                        ->orWhereRelation('consultations', 'admission_status', '=', 'Observation');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getPaginatedAncConsultedVisits($data, DataTableQueryParams $params, User $user)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where(function (Builder $query) use($params) {
                        $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('consultations', 'icd11_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('consultations', 'admission_status', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                    })
                    
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'My Patients'){
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('doctor_done_by', null)
                    ->where('user_id', '=', $user->id)
                    ->whereRelation('patient', 'patient_type', '=', 'ANC')
                    ->where('closed', null)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('doctor_done_by', null)
                    ->whereRelation('patient', 'patient_type', '=', 'ANC')
                    ->where('closed', null)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getConsultedVisitsTransformer(): callable
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
                'payPercent'        => $visit->totalBills() ? round((float)($visit->totalPayments() / $visit->totalBills()) * 100) : null,
                'payPercentNhis'    => $visit->totalBills() ? round((float)($visit->totalPayments() / ($visit->totalBills()/10)) * 100) : null,
                'payPercentHmo'     => $visit->totalBills() ? round((float)($visit->totalApprovedBills() / $visit->totalBills()) * 100) : null,

            ];
         };
    }

    public function initiateConsultation(Visit $visit, Request $request) 
    {
        $visit->update([
            'doctor_id'    =>  $request->user()->id
        ]);

        return response()->json(new PatientBioResource($visit));
    }
}