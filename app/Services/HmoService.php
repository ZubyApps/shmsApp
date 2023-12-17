<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

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
                ->where(function (Builder $query) {
                    $query->whereRelation('visit.sponsor.sponsorCategory', 'name', 'HMO')
                    ->orWhereRelation('visit.sponsor.sponsorCategory', 'name', 'NHIS');
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
                'diagnosis'         => $prescription->consultation?->icd11_diagnosis,
                'resource'          => $prescription->resource->name,
                'prescription'      => $prescription->prescription,
                'quantity'          => $prescription->qty_billed,
                'bill'              => $prescription->bill,
                'billed'            => $prescription->bill_date ? (new Carbon($prescription->bill_date))->format('d/m/y g:ia') : '',
                'approved_status'   => $prescription->approved,
            ];
         };
    }

    public function approve($data, Prescription $prescription, User $user)
    {
        return  $prescription->update([
            'approved'         => true,
            'approval_comment' => $data->comment,
            'approval_by'      => $user->id,
        ]);
    }

    public function reject($data, Prescription $prescription, User $user)
    {
        return  $prescription->update([
            'rejected'          => true,
            'rejection_comment' => $data->comment,
            'rejected_by'       => $user->id,
        ]);
    }
}