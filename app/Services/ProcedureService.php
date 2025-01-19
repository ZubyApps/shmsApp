<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Prescription;
use App\Models\Procedure;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProcedureService
{
    public function __construct(private readonly Procedure $procedure)
    {
    }

    public function create(Prescription $prescription, User $user): Procedure
    {
        return $user->procedures()->create([
            'prescription_id'   => $prescription->id,
            'user_id'           => $user->id,
        ]);
    }

    public function update(Request $data, Procedure $procedure, User $user): Procedure
    {
       $procedure->update([
            'booked_date'     => $data->bookedDate,
            'comment'         => $data->comment,
            'date_booked_by'  => $user->id,
        ]);

        return $procedure;
    }

    public function getPaginatedProcedures(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->procedure
                        ->whereRelation('prescription.resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('prescription.visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('prescription.visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('prescription.visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->pending){
            if ($data->hmo){
                return $this->procedure
                    ->where(function(Builder $query) {
                        $query->whereRelation('prescription.visit.sponsor', 'category_name', '=', 'HMO')
                        ->orWhereRelation('prescription.visit.sponsor', 'category_name', '=', 'NHIS')
                        ->orWhereRelation('prescription.visit.sponsor', 'category_name', '=', 'Retainership');
                    })
                    ->whereNull('status')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            if ($data->cash){
                return $this->procedure
                    ->where(function(Builder $query) {
                        $query->whereRelation('prescription.visit.sponsor', 'category_name', '=', 'Individual')
                        ->orWhereRelation('prescription.visit.sponsor', 'category_name', '=', 'Family');
                    })
                    ->whereNull('status')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            return $this->procedure
                    ->whereNull('status')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->procedure
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (Procedure $procedure) {
            return [
                'id'                => $procedure->id,
                'patient'           => $procedure->prescription->visit->patient->patientId(),
                'phone'             => $procedure->prescription->visit->patient->phone,
                'prescribedBy'      => $procedure->user->username,
                'sponsor'           => $procedure->prescription->visit->sponsor->name,
                'sponsorCat'        => $procedure->prescription->visit->sponsor->sponsorCategory->name,
                'procedure'         => $procedure->prescription->resource->name,
                'bookedDate'        => $procedure->booked_date ? (new Carbon($procedure->booked_date))->format('D d/m/y') : '',
                'dateBookedBy'      => $procedure?->dateBookedBy?->username,
                'comment'           => $procedure->comment,
                'status'            => $procedure->status,
                'statusUpdatedBy'   => $procedure?->statusUpdatedBy?->username,
                'createdAt'         => (new Carbon($procedure->created_at))->format('d/m/y'),
            ];
         };
    }

    public function updateStatus(Request $data, Procedure $procedure, User $user)
    {
        return $procedure->update([
            'status'            => $data->status ? $data->status : null,
            'status_updated_by' => $user->id
        ]);
    }
}
