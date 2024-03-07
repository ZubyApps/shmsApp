<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function getPaginatedWaitingVisits(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';

        if (! empty($params->searchTerm)) {
            return $this->visit
                        ->Where('consulted', null)
                        // ->where('closed', false)
                        ->where(function (Builder $query) use($params) {
                            $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        
        return $this->visit
                    ->where('consulted', null)
                    ->where('closed', false)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getWaitingListTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'patientId'         => $visit->patient->id,
                'ancRegId'          => $visit->antenatalRegisteration?->id,
                'patient'           => $visit->patient->patientId(),
                'sex'               => $visit->patient->sex,
                'age'               => $visit->patient->age(),
                'sponsor'           => $visit->sponsor->name,
                'sponsorCategory'   => $visit->sponsor->category_name,
                'came'              => (new Carbon($visit->created_at))->diffForHumans(['parts' => 2, 'short' => true]),
                'doctor'            => $visit->doctor->username ?? '',
                'patientType'       => $visit->patient->patient_type,
                'status'            => $visit->status,
                'vitalSigns'        => $visit->vitalSigns->count(),
                'ancVitalSigns'     => $visit->antenatalRegisteration?->ancVitalSigns->count(),
                'emergency'         => $visit->prescriptions->where('consultation_id', null)->count(),
                'closed'            => $visit->closed,
            ];
         };
    }

    public function changeVisitSponsor(Request $data, Visit $visit, User $user)
    {
        $visit->update([
            "sponsor_id" => $data->sponsor,
            "sponsor_changed_by" => $user->id,
        ]);

        return $visit;
    }

    public function discharge(Request $data, Visit $visit, User $user)
    {
        $visit->update([
            "discharge_reason"  => $data->reason ? $data->reason : null,
            "discharge_remark"  => $data->reason ? $data->remark : null,
            "doctor_done_by"    => $data->reason ? $user->id : null,
        ]);

        return $visit;
    }

    public function close(User $user, Visit $visit)
    {
        return DB::transaction(function () use($user, $visit){
            $visit->update([
                'closed'            => true, 
                'closed_opened_at'  => new Carbon(), 
                'closed_opened_by'  => $user->id
            ]);

            $visit->patient()->update(['is_active' => false]);
        });
    }

    public function open(User $user, Visit $visit)
    {
        return DB::transaction(function () use($user, $visit){
            $visit->update([
                'closed'            => false, 
                'closed_opened_at'  => new Carbon(), 
                'closed_opened_by'  => $user->id
            ]);
            $visit->patient()->update(['is_active' => true]);
        });
    }

    public function delete($visit)
    {
        return DB::transaction(function() use($visit){
            $visit->patient()->update(['is_active' => false]);
            $visit->destroy($visit->id);
        });
    }
}
