<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Http\Resources\PatientBioResource;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class VisitService
{
    public function __construct(private readonly Visit $visit)
    {
    }

    public function create(Request $data, User $user): Visit
    {
        $patient = Patient::findOrFail($data->patientId);

        $patient->update([
            "is_active" => true
        ]); 

        return $user->visits()->create([
                "patient_id" => $data->patientId,
                "sponsor_id" => $patient->sponsor->id,
        ]);
    }

    public function update(Request $data, Visit $visit, User $user): Visit
    {
       $visit->update([
                "patient_type"   => $data->patientType,
        ]);
        return $visit;
    }

    public function getPaginatedWaitingVisits(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';

        if (! empty($params->searchTerm)) {
            return $this->visit
                        ->Where('consulted', null)
                        ->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('consulted', null)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getWaitingListTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'patientId'         => $visit->patient->id,
                'patient'           => $visit->patient->card_no.' ' .$visit->patient->first_name.' '. $visit->patient->middle_name.' '.$visit->patient->last_name,
                'sex'               => $visit->patient->sex,
                'age'               => str_replace(['a', 'g', 'o'], '', (new Carbon($visit->patient->date_of_birth))->diffForHumans(['other' => null, 'parts' => 1, 'short' => true]), ),
                'sponsor'           => $visit->sponsor->name,
                'came'              => (new Carbon($visit->created_at))->diffForHumans(['parts' => 2, 'short' => true]),
                'doctor'            => $visit->doctor->username ?? '',
                'patientType'       => $visit->patient->patient_type,
                'status'            => $visit->status,
                'vitalSigns'        => $visit->vitalSigns->count()
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

    public function getPaginatedConsultedVisits(DataTableQueryParams $params)
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
                        ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                    })
                    
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('consulted', '!=', null)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getConsultedVisitsTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patient'           => $visit->patient->card_no.' ' .$visit->patient->first_name.' '. $visit->patient->middle_name.' '.$visit->patient->last_name,
                'doctor'            => $visit->doctor->username,
                'diagnosis'         => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()->icd11_diagnosis,
                'sponsor'           => $visit->sponsor->name,
                'admissionStatus'   => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()->admission_status,
                'patientType'       => $visit->patient->patient_type,

            ];
         };
    }

    public function getPaginatedConsultedVisitsNurses(DataTableQueryParams $params)
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
                        ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                    })
                    
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('consulted', '!=', null)
                    ->whereRelation('consultations.prescriptions.resource.resourceSubcategory.resourceCategory', 'name', 'Medication')
                    ->orWhereRelation('consultations.prescriptions.resource.resourceSubcategory.resourceCategory', 'name', 'Medical Service')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getConsultedVisitsNursesTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patient'           => $visit->patient->card_no.' ' .$visit->patient->first_name.' '. $visit->patient->middle_name.' '.$visit->patient->last_name,
                'doctor'            => $visit->doctor->username,
                'diagnosis'         => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()->icd11_diagnosis,
                'sponsor'           => $visit->sponsor->name,
                'admissionStatus'   => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()->admission_status,
                'patientType'       => $visit->patient->patient_type,
                'vitalSigns'        => $visit->vitalSigns->count(),
                'chartCount'        => Prescription::where('visit_id', $visit->id)->count()
            ];
         };
    }

}