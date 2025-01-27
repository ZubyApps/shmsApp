<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Consultation;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Visit;
use App\Models\Ward;
use App\Notifications\InvestigationNotifier;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvestigationService
{
    public function __construct(
        private readonly Visit $visit, 
        private readonly Prescription $prescription,
        private readonly PayPercentageService $payPercentageService,
        private readonly HelperService $helperService,
        private readonly InvestigationNotifier $investigationNotifier,
        private readonly Ward $ward,
        )
    {
        
    }

    public function getpaginatedFilteredLabVisits(DataTableQueryParams $params, $data)
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
            ->whereHas('prescriptions', function(Builder $query){
                $query->where('result', '=', null)
                ->whereRelation('resource', 'category', '=', 'Investigations')
                ->whereRelation('resource', 'sub_category', '!=', 'Imaging');
            })
            ->where('admission_status', '=', 'Outpatient')
            ->whereRelation('patient', 'patient_type', '!=', 'ANC')
            ->orderBy($orderBy, $orderDir)
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Inpatient'){
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('closed', false)
                    ->whereHas('prescriptions', function(Builder $query){
                            $query->where('result', '=', null)
                            ->whereRelation('resource', 'category', '=', 'Investigations')
                            ->whereRelation('resource', 'sub_category', '!=', 'Imaging');
                        })
                    ->where(function (Builder $query) {
                        $query->where('admission_status', '=', 'Inpatient')
                        ->orWhere('admission_status', '=', 'Observation');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
        if ($data->filterBy == 'ANC'){
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('closed', false)
                    ->whereHas('prescriptions', function(Builder $query){
                        $query->where('result', '=', null)
                        ->whereRelation('resource', 'category', '=', 'Investigations')
                        ->whereRelation('resource', 'sub_category', '!=', 'Imaging');
                    })
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
        $ward = $this->ward->where('id', $visit->ward)->first();
            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patient'           => $visit->patient->patientId(),
                'doctor'            => $visit->doctor->username,
                'diagnosis'         => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->icd11_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->provisional_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->assessment,
                'sponsor'           => $visit->sponsor->name,
                'admissionStatus'   => $visit->admission_status,
                'ward'              => $ward ? $this->helperService->displayWard($ward) : '',
                'wardId'            => $visit->ward ?? '',
                'wardPresent'       => $ward?->visit_id == $visit->id,
                'patientType'       => $visit->patient->patient_type,
                'labPrescribed'     => Prescription::where('visit_id', $visit->id)
                                        ->whereRelation('resource', 'category', '=', 'Investigations')
                                        ->whereRelation('resource', 'sub_category', '!=', 'Imaging')
                                        ->count(),
                'labDone'           => Prescription::where('visit_id', $visit->id)
                                        ->whereRelation('resource', 'category', '=', 'Investigations')
                                        ->whereRelation('resource', 'sub_category', '!=', 'Imaging')
                                        ->where('result_date','!=', null)
                                        ->count(),
                'sponsorCategory'   => $visit->sponsor->sponsorCategory->name,
                'payPercent'        => $this->payPercentageService->individual_Family($visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'doctorDoneAt'      => $visit->doctor_done_at ? (new Carbon($visit->doctor_done_at))->format('d/m/y g:ia') : '',
                'discharged'        => $visit->discharge_reason,
                'reason'            => $visit->discharge_reason,
                'closed'            => $visit->closed,
                'closedBy'          => $visit->closedOpenedBy?->username,
                'flagSponsor'       => $visit->sponsor->flag,
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient?->flag_reason,
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
                    ->whereRelation('resource', 'sub_category', '!=', 'Imaging')
                    ->where(function (Builder $query) use($params) {
                        $query->whereRelation('visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('visit.patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('resource', 'sub_category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                    ->whereRelation('visit', 'consulted', '!=', null)
                    ->where('discontinued', false)
                    ->where('result_date', null)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->prescription
                    ->whereRelation('resource', 'category', 'Investigations')
                    ->whereRelation('resource', 'sub_category', '!=', 'Imaging')
                    ->whereRelation('visit', 'consulted', '!=', null)
                    ->where(function (Builder $query) {
                        $query->whereRelation('consultation', 'admission_status', '=', 'Inpatient')
                        ->orWhereRelation('consultation', 'admission_status', '=', 'Observation');
                    })
                    ->where('result_date', null)
                    ->where('discontinued', false)
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
                        ->whereRelation('resource', 'sub_category', '!=', 'Imaging')
                        ->where(function (Builder $query) use($params) {
                            $query->whereRelation('visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('visit.patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('resource', 'sub_category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                            })
                        ->where('discontinued', false)
                        ->whereRelation('visit', 'consulted', '!=', null)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->notLab){
            return $this->prescription
                    ->whereRelation('resource', 'category', 'Investigations')
                    ->whereRelation('resource', 'sub_category', '!=', 'Imaging')
                    ->whereRelation('visit', 'consulted', '!=', null)
                    ->whereRelation('consultation', 'admission_status', '=', 'Outpatient')
                    ->where('created_at', '>', (new Carbon)->subDays(2))
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->prescription
                    ->whereRelation('resource', 'category', 'Investigations')
                    ->whereRelation('resource', 'sub_category', '!=', 'Imaging')
                    ->whereRelation('visit', 'consulted', '!=', null)
                    ->whereRelation('consultation', 'admission_status', '=', 'Outpatient')
                    ->where('discontinued', false)
                    ->where('result_date', null)
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
                'sponsorCategory'       => $prescription->visit->sponsor->sponsorCategory->name,
                'sponsorCategoryClass'  => $prescription->visit->sponsor->sponsorCategory->pay_class,
                'approved'          => $prescription->approved,
                'rejected'          => $prescription->rejected,
                'paid'              => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill,
                'paidNhis'          => $prescription->paid > 0 && $prescription->approved && $prescription->paid >= $prescription->hms_bill/10 && $prescription->visit->sponsor->sponsorCategory->name == 'NHIS',
            ];
         };
    }

    public function createLabResultRecord(Request $data, Prescription $prescription, User $user): Prescription
    {
        return DB::transaction(function () use($data, $prescription, $user) {
            $resource = $prescription->resource;
    
            $prescription->update([
                'test_sample'    => $data->sample,
                'result'         => $data->result,
                'result_date'    => Carbon::now(),
                'result_by'      => $user->id,
                'discontinued'      => false,
                'dispense_comment'  => null,
                ]);
            
            $resource->update([
                'stock_level' => $resource->stock_level - 1
            ]);
    
            // if ($prescription->visit->patient->sms){
            //     $this->investigationNotifier->toSms($prescription);
            // }
    
            return $prescription;

        }, 2);
    }

    public function updateLabResultRecord(Request $data, Prescription $prescription, User $user): Prescription
    {
        $prescription->update([
            'test_sample'       => $data->sample,
            'result'            => $data->result,
            // 'result_date'       => Carbon::now(),
            // 'result_by'         => $user->id,
            'discontinued'      => false,
            'dispense_comment'  => null,
            ]);

        return $prescription;
    }

    public function removeLabResultRecord(Prescription $prescription): Prescription
    {
        $resource = $prescription->resource;

        $prescription->update([
            'test_sample'       => null,
            'result'            => null,
            'result_date'       => null,
            'result_by'         => null,
            'discontinued'      => false,
            'dispense_comment'  => null,
            ]);

            $resource->update([
                'stock_level' => $resource->stock_level + 1
            ]);

        return  $prescription;
    }

    public function removetTestFromList($data, Prescription $prescription, User $user): Prescription
    {
        $prescription->update([
            'discontinued'      => true,
            'discontinued_by'   => $user->id,
            'dispense_comment'  => $data->removalReason,
            ]);

        return  $prescription;
    }

    public function getAllPatientsVisitsTests(Visit $visit)
    {   
            return $this->prescription
                        ->where('visit_id', $visit->id)
                        ->whereRelation('resource', 'category', 'Investigations')
                        ->whereRelation('resource', 'sub_category', '!=', 'Imaging')
                        ->whereRelation('visit', 'consulted', '!=', null)
                        ->where('result_date', '!=', null)
                        ->orderBy('created_at', 'asc')
                        ->get();
    }
}
