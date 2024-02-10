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

class InvestigationService
{
    public function __construct(
        private readonly Visit $visit, 
        private readonly Prescription $prescription,
        private readonly PayPercentageService $payPercentageService
        )
    {
        
    }

    public function getpaginatedFilteredLabVisits(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';

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
            ->whereRelation('prescriptions.resource', 'category', '=', 'Investigations')
            ->whereRelation('consultations', 'admission_status', '=', 'Outpatient')
            ->whereRelation('patient', 'patient_type', '!=', 'ANC')
            ->orderBy($orderBy, $orderDir)
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Inpatient'){
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('closed', false)
                    ->whereRelation('prescriptions.resource', 'category', '=', 'Investigations')
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
                    ->where('closed', false)
                    ->whereRelation('prescriptions.resource', 'category', '=', 'Investigations')
                    ->whereRelation('patient', 'patient_type', '=', 'ANC')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('closed', false)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getConsultedVisitsLabTransformer(): callable
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
                'closed'            => $visit->closed,
            ];
         };
    }

    public function getInpatientLabRequests(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->prescription
                    ->whereRelation('resource', 'category', 'Investigations')
                    ->where(function (Builder $query) use($params) {
                        $query->whereRelation('visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('visit.patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('resource', 'sub_category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                    ->whereRelation('visit', 'consulted', '!=', null)
                    ->where('result_date', null)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->prescription
                    ->whereRelation('resource', 'category', 'Investigations')
                    ->whereRelation('visit', 'consulted', '!=', null)
                    ->where(function (Builder $query) {
                        $query->whereRelation('consultation', 'admission_status', '=', 'Inpatient')
                        ->orWhereRelation('consultation', 'admission_status', '=', 'Observation');
                    })
                    ->where('result_date', null)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getOutpatientLabRequests(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->prescription
                        ->whereRelation('resource', 'category', 'Investigations')
                        ->where(function (Builder $query) use($params) {
                            $query->whereRelation('visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('visit.patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('resource', 'sub_category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                            })
                        ->whereRelation('visit', 'consulted', '!=', null)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->prescription
                    ->whereRelation('resource', 'category', 'Investigations')
                    ->whereRelation('visit', 'consulted', '!=', null)
                    ->whereRelation('consultation', 'admission_status', '=', 'Outpatient')
                    ->where('created_at', '>', (new Carbon)->subDays(2))
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getLabTransformer(): callable
    {
       return  function (Prescription $prescription) {
            return [
                'id'                => $prescription->id,
                'requested'         => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                'type'              => $prescription->resource->resourceSubCategory->name,
                'doctor'            => $prescription->user->username,
                'patient'           => $prescription->visit->patient->patientId(),
                'sponsor'           => $prescription->visit->patient->sponsor->name,
                'diagnosis'         => $prescription->consultation->icd11_diagnosis ??
                                       $prescription->consultation->provisional_diagnosis ??
                                       $prescription->consultation->assessment,
                'resource'          => $prescription->resource->name,
                'result'            => $prescription->result_date,
            ];
         };
    }

    public function createLabResultRecord(Request $data, Prescription $prescription, User $user): Prescription
    {
        $resource = $prescription->resource;

        $prescription->update([
            'test_sample'    => $data->sample,
            'result'         => $data->result,
            'result_date'    => Carbon::now(),
            'result_by'      => $user->id,
            ]);
        
        $resource->update([
            'stock_level' => $resource->stock_level - 1
        ]);

        return $prescription;
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
        $resource = $prescription->resource;

        $prescription->update([
            'test_sample'    => null,
            'result'         => null,
            'result_date'    => null,
            'result_by'      => null,

            ]);

            $resource->update([
                'stock_level' => $resource->stock_level + 1
            ]);

        return  $prescription;
    }

}
