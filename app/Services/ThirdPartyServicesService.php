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
        return $user->thirdPartyServies()->create([
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

        if (! empty($params->searchTerm)) {
            return $this->thirdPartyService
                        ->whereRelation('thirdParty', 'full_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
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

        return $this->thirdPartyService
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (ThirdPartyService $thirdPartyService) {
            return [
                'id'                => $thirdPartyService->id,
                'date'              => (new Carbon($thirdPartyService->created_at))->format('d/m/Y'),
                'thirdParty'        => $thirdPartyService->thirdParty->short_name,
                'sponsorCategoryClass'  => $thirdPartyService->prescription->visit->sponsor->sponsorCategory->pay_class,
                'sponsorCategory'       => $thirdPartyService->prescription->visit->sponsor->category_name,
                'sponsor'               => $thirdPartyService->prescription->visit->sponsor->name,
                'resource'              => $thirdPartyService->prescription->resource->name,
                'patient'               => $thirdPartyService->prescription->visit->patient->patientId(),
                'doctor'                => $thirdPartyService->prescription->user->username,
                'diagnosis'             => $thirdPartyService->prescription->consultation?->icd11_diagnosis ?? $thirdPartyService->prescription->consultation?->provisional_diagnosis ?? $thirdPartyService->prescription->consultation?->assessment,
                'admissionStatus'   => $thirdPartyService->prescription->visit->admission_status,
                'reason'            => $thirdPartyService->prescription->visit->discharge_reason,
                'hmsBill'           => $thirdPartyService->prescription->hms_bill,
                'initiatedBy'       => $thirdPartyService->user->username,
                'payPercent'        => $this->payPercentageService->individual_Family($thirdPartyService->prescription->visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($thirdPartyService->prescription->visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($thirdPartyService->prescription->visit),
                'paid'              => $thirdPartyService->prescription->paid > 0 && $thirdPartyService->prescription->paid >= $thirdPartyService->prescription->hms_bill,
                'paidNhis'          => $thirdPartyService->prescription->paid > 0 && $thirdPartyService->prescription->paid >= $thirdPartyService->prescription->hms_bill/10 && $thirdPartyService->prescription->visit->sponsor->sponsorCategory->name == 'NHIS',
                'approved'          => $thirdPartyService->prescription->approved, 
                'rejected'          => $thirdPartyService->prescription->rejected,
                'user'              => auth()->user()->designation->access_level > 4
            ];
         };
    }
}
