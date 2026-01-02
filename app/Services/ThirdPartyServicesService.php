<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Prescription;
use App\Models\ThirdPartyService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ThirdPartyServicesService
{
    public function __construct(
        private readonly ThirdPartyService $thirdPartyService,
        private readonly PayPercentageService $payPercentageService
        )
    {
    }

    public function create(Request $data, Prescription $prescription, User $user): ThirdPartyService
    {
        return $user->thirdPartyServices()->create([
            'prescription_id'       => $prescription->id,
            'third_party_id'        => $data->thirdParty,
        ]);
    }

    public function update(Request $data, ThirdPartyService $thirdPartyService, User $user): ThirdPartyService
    {
       $thirdPartyService->update([
            'prescription_id'       => $data->prescription,
            'third_party_id'        => $data->thirdParty,
            'user_id'               => $user->id
        ]);

        return $thirdPartyService;
    }

    public function getPaginatedThirdPartyServices(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query      = $this->thirdPartyService->select('id', 'user_id', 'third_party_id', 'prescription_id', 'created_at')
                        ->with([
                            'user:id,username', 
                            'thirdParty:id,short_name', 
                            'prescription' => function($query){
                                $query->select('id', 'consultation_id', 'resource_id', 'visit_id', 'user_id', 'hms_bill', 'paid', 'approved', 'rejected')
                                ->with([
                                        'visit' => function($query){
                                            $query->select('id', 'sponsor_id', 'patient_id', 'admission_status', 'discharge_reason')
                                            ->with([
                                                'sponsor' => function($query){
                                                    $query->select('id', 'sponsor_category_id', 'name', 'category_name')
                                                    ->with(['sponsorCategory:id,pay_class']);
                                                    },
                                                'patient:id,first_name,middle_name,last_name,card_no'
                                            ]);
                                        },
                                        'consultation:id,icd11_diagnosis,provisional_diagnosis,assessment',
                                        'resource:id,name',
                                        'user:id,username'
                                ]);
                            }
                        ]);

        if (! empty($params->searchTerm)) {
            return $query->whereRelation('thirdParty', 'full_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere(function (Builder $query) use($params) {
                            $query->whereRelation('prescription.visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('prescription.visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('prescription.visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('prescription.visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('prescription.visit.sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('prescription.visit.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('prescription.consultation', 'icd11_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('prescription.consultation', 'provisional_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (ThirdPartyService $thirdPartyService) {
            $visit          = $thirdPartyService->prescription->visit;
            $consultation   = $thirdPartyService->prescription->consultation;
            $resource       = $thirdPartyService->prescription->resource;
            return [
                'id'                => $thirdPartyService->id,
                'date'              => (new Carbon($thirdPartyService->created_at))->format('d/m/Y'),
                'thirdParty'        => $thirdPartyService->thirdParty->short_name,
                'sponsorCategoryClass'  => $visit->sponsor->sponsorCategory->pay_class,
                'sponsorCategory'       => $visit->sponsor->category_name,
                'sponsor'               => $visit->sponsor->name,
                'resource'              => $resource->name,
                'patient'               => $visit->patient->patientId(),
                'doctor'                => $thirdPartyService->prescription->user->username,
                'diagnosis'             => $consultation?->icd11_diagnosis ?? $consultation?->provisional_diagnosis ?? $consultation?->assessment,
                'admissionStatus'   => $visit->admission_status,
                'reason'            => $visit->discharge_reason,
                'hmsBill'           => $thirdPartyService->prescription->hms_bill,
                'initiatedBy'       => $thirdPartyService->user->username,
                'payPercent'        => $this->payPercentageService->individual_Family($visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'paid'              => $thirdPartyService->prescription->paid > 0 && $thirdPartyService->prescription->paid >= $thirdPartyService->prescription->hms_bill,
                'paidNhis'          => $thirdPartyService->prescription->paid > 0 && $thirdPartyService->prescription->paid >= $thirdPartyService->prescription->hms_bill/10 && $visit->sponsor->category_name == 'NHIS',
                'approved'          => $thirdPartyService->prescription->approved, 
                'rejected'          => $thirdPartyService->prescription->rejected,
                'user'              => auth()->user()->designation->access_level > 4
            ];
         };
    }
}
