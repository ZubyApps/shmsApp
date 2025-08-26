<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Jobs\SendTestResultDone;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Visit;
use App\Models\Ward;
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
        private readonly Ward $ward,
        )
    {
        
    }

    public function getpaginatedFilteredLabVisits(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'consulted';
        $orderDir   =  'desc';
        $query = $this->visit::with([
            'sponsor', 
            'consultations', 
            'patient', 
            'prescriptions', 
            'doctor', 
            'closedOpenedBy',
            'payments'
        ])
        ->withCount([
            'prescriptions as labPrescribed' => function (Builder $query) {
            $query->whereRelation('resource', 'category', '=', 'Investigations')
                    ->whereRelation('resource', 'sub_category', '!=', 'Imaging');
            }, 
            'prescriptions as labDone' => function (Builder $query) {
            $query->whereRelation('resource', 'category', '=', 'Investigations')
                    ->whereRelation('resource', 'sub_category', '!=', 'Imaging')
                    ->where('result_date', '!=', null);
            },
        ])
        ->whereNotNull('consulted');

        if (! empty($params->searchTerm)) {

            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';

            if ($data->filterBy == 'ANC'){
                return $query->where('visit_type', '=', 'ANC')
                    ->where(function (Builder $query) use($searchTerm) {
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
            
            $query = $query->where(function (Builder $query) use($searchTerm) {
                        $query->where('created_at', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
                        ->orWhereRelation('consultations', 'icd11_diagnosis', 'LIKE', $searchTerm)
                        ->orWhereRelation('consultations', 'admission_status', 'LIKE', $searchTerm)
                        ->orWhereRelation('sponsor', 'name', 'LIKE', $searchTerm)
                        ->orWhereRelation('sponsor', 'category_name', 'LIKE', $searchTerm);
                    });
                    
            return $this->helperService->paginateQuery($query, $params);
        }

        if ($data->filterBy == 'Outpatient'){
            $query->where('admission_status', '=', 'Outpatient')
                ->where('visit_type', '!=', 'ANC');
        }

        if ($data->filterBy == 'Inpatient'){
            $query->where(function (Builder $query) {
                        $query->where('admission_status', '=', 'Inpatient')
                        ->orWhere('admission_status', '=', 'Observation');
                    });
        }
        if ($data->filterBy == 'ANC'){
            $query->where('visit_type', '=', 'ANC');
        }

        $query = $this->generalFilters($query);
        return $this->helperService->paginateQuery($query, $params);
    }

    private function generalFilters(Builder $query)
    {
        return $query->where('closed', false)
                    ->whereHas('prescriptions', function(Builder $query){
                        $query->where('result', '=', null)
                        // ->where('discontinued', false)
                        ->where('dispense_comment', null)
                        ->whereRelation('resource', 'category', '=', 'Investigations')
                        ->whereRelation('resource', 'sub_category', '!=', 'Imaging');
                    });
    }

    public function getConsultedVisitsLabTransformer(): callable
    {
       return  function (Visit $visit) {
        $latestConsultation = $visit->consultations->sortDesc()->first();
        $ward = $this->ward->where('id', $visit->ward)->first();
            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patient'           => $visit->patient->patientId(),
                'doctor'            => $visit->doctor->username,
                'diagnosis'         => $latestConsultation?->icd11_diagnosis ?? 
                                       $latestConsultation?->provisional_diagnosis ?? 
                                       $latestConsultation?->assessment,
                'sponsor'           => $visit->sponsor->name,
                'admissionStatus'   => $visit->admission_status,
                'ward'              => $ward ? $this->helperService->displayWard($ward) : '',
                'wardId'            => $visit->ward ?? '',
                'wardPresent'       => $ward?->visit_id == $visit->id,
                'visitType'         => $visit->visit_type,
                'labPrescribed'     => $visit->labPrescribed,
                'labDone'           => $visit->labDone,
                'sponsorCategory'   => $visit->sponsor->category_name,
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
        $query = $this->prescription::with([
            'resource', 
            'user', 
            'visit' => function ($query) {
                $query->with([
                    'sponsor.sponsorCategory',
                    'patient'
                ]);
            },
            'consultation',
        ]);

        if (! empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            return $query->whereRelation('resource', 'category', 'Investigations')
                    ->whereRelation('resource', 'sub_category', '!=', 'Imaging')
                    ->where(function (Builder $query) use($searchTerm) {
                        $query->whereRelation('visit.patient', 'first_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('visit.patient', 'last_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('visit.patient', 'card_no', 'LIKE', $searchTerm)
                        ->orWhereRelation('visit.patient.sponsor', 'category_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('resource', 'sub_category', 'LIKE', $searchTerm);
                        })
                    ->whereRelation('visit', 'consulted', '!=', null)
                    // ->where('discontinued', false)
                    ->where('result_date', null)
                    // ->where('dispense_comment', null)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->whereRelation('resource', 'category', 'Investigations')
                    ->whereRelation('resource', 'sub_category', '!=', 'Imaging')
                    ->whereRelation('visit', 'consulted', '!=', null)
                    ->where(function (Builder $query) {
                        $query->whereRelation('consultation', 'admission_status', '=', 'Inpatient')
                        ->orWhereRelation('consultation', 'admission_status', '=', 'Observation');
                    })
                    ->where('result_date', null)
                    // ->where('discontinued', false)
                    ->where('dispense_comment', null)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getOutpatientLabRequests(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query = $this->prescription::with([
            'resource', 
            'user', 
            'consultation',
            'visit' => function ($query) {
                $query->with([
                    'sponsor.sponsorCategory',
                    'patient'
                ]);
            }
        ]);

        if (! empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            return $query->whereRelation('resource', 'category', 'Investigations')
                        ->whereRelation('resource', 'sub_category', '!=', 'Imaging')
                        ->where(function (Builder $query) use($searchTerm) {
                            $query->whereRelation('visit.patient', 'first_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('visit.patient', 'last_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('visit.patient', 'card_no', 'LIKE', $searchTerm)
                            ->orWhereRelation('visit.patient.sponsor', 'category_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('resource', 'sub_category', 'LIKE', $searchTerm);
                            })
                        // ->where('discontinued', false)
                        ->where('dispense_comment', null)
                        ->whereRelation('visit', 'consulted', '!=', null)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->notLab){
            return $query->whereRelation('resource', 'category', 'Investigations')
                    ->whereRelation('resource', 'sub_category', '!=', 'Imaging')
                    ->whereRelation('visit', 'consulted', '!=', null)
                    ->whereRelation('consultation', 'admission_status', '=', 'Outpatient')
                    ->where('created_at', '>', (new Carbon)->subDays(2))
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->whereRelation('resource', 'category', 'Investigations')
                    ->whereRelation('resource', 'sub_category', '!=', 'Imaging')
                    ->whereRelation('visit', 'consulted', '!=', null)
                    ->whereRelation('consultation', 'admission_status', '=', 'Outpatient')
                    // ->where('discontinued', false)
                    ->where('dispense_comment', null)
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
                'type'              => $prescription->resource->category,
                'doctor'            => $prescription->user->username,
                'patient'           => $prescription->visit->patient->patientId(),
                'sponsor'           => $prescription->visit->sponsor->name,
                'diagnosis'         => $prescription->consultation->icd11_diagnosis ??
                                       $prescription->consultation->provisional_diagnosis ??
                                       $prescription->consultation->assessment,
                'resource'          => $prescription->resource->name,
                'result'            => $prescription->result_date,
                'sponsorCategory'       => $prescription->visit->sponsor->category_name,
                'sponsorCategoryClass'  => $prescription->visit->sponsor->sponsorCategory->pay_class,
                'approved'          => $prescription->approved,
                'rejected'          => $prescription->rejected,
                'paid'              => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill,
                'paidNhis'          => $prescription->paid > 0 && $prescription->approved && $prescription->paid >= $prescription->hms_bill/10 && $prescription->visit->sponsor->category_name == 'NHIS',
                'collected'         => $prescription->discontinued ? true : false,
                'collectedBy'       => $prescription->discontinuedBy?->username,
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
    
            if ($prescription->visit->patient->sms){
                SendTestResultDone::dispatch($prescription)->delay(5);
            }
    
            return $prescription;

        }, 2);
    }

    public function updateLabResultRecord(Request $data, Prescription $prescription, User $user): Prescription
    {
        $prescription->update([
                'test_sample'       => $data->sample,
                'result'            => $data->result,
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
            // 'discontinued'      => true,
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
                        // ->whereRelation('resource', 'sub_category', '!=', 'Imaging')
                        ->whereRelation('visit', 'consulted', '!=', null)
                        ->where('result_date', '!=', null)
                        ->orderBy('created_at', 'asc')
                        ->get();
    }
}
