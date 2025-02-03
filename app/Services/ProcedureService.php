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
        $query = $this->procedure::with([
            'prescription' => function($query) {
                $query->select('id', 'approved', 'rejected', 'nhis_bill', 'paid', 'visit_id', 'resource_id')
                ->with([
                    'visit.sponsor.sponsorCategory',
                    'visit.patient',
                    'resource'
                ]);
            },
            'user',
            'dateBookedBy',
        ]);

        if (! empty($params->searchTerm)) {
            return $query->whereRelation('prescription.resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('prescription.visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('prescription.visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('prescription.visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->pending){
            if ($data->hmo){
                return $query->where(function(Builder $query) {
                        $query->whereRelation('prescription.visit.sponsor', 'category_name', '=', 'HMO')
                        ->orWhereRelation('prescription.visit.sponsor', 'category_name', '=', 'NHIS')
                        ->orWhereRelation('prescription.visit.sponsor', 'category_name', '=', 'Retainership');
                    })
                    ->whereNull('status')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            if ($data->cash){
                return $query->where(function(Builder $query) {
                        $query->whereRelation('prescription.visit.sponsor', 'category_name', '=', 'Individual')
                        ->orWhereRelation('prescription.visit.sponsor', 'category_name', '=', 'Family')
                        ->orWhereRelation('prescription.visit.sponsor', 'category_name', '=', 'NHIS');
                    })
                    ->whereNull('status')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            return $query->whereNull('status')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
        return  function (Procedure $procedure) {
           $prescription = $procedure->prescription;
           
            return [
                'id'                => $procedure->id,
                'patient'           => $prescription->visit->patient->patientId(),
                'phone'             => $prescription->visit->patient->phone,
                'prescribedBy'      => $procedure->user->username,
                'sponsor'           => $prescription->visit->sponsor->name,
                'sponsorCat'        => $prescription->visit->sponsor->category_name,
                'procedure'         => $prescription->resource->name,
                'bookedDate'        => $procedure->booked_date ? (new Carbon($procedure->booked_date))->format('D d/m/y g:ia') : '',
                'dateBookedBy'      => $procedure?->dateBookedBy?->username,
                'comment'           => $procedure->comment,
                'status'            => $procedure->status,
                'statusUpdatedBy'   => $procedure?->statusUpdatedBy?->username,
                'approved'          => $prescription->approved,
                'rejected'          => $prescription->rejected,
                'paid'              => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill,
                'paidNhis'          => $prescription->paid > 0 && $prescription->approved && $prescription->paid >= $prescription->nhis_bill && $prescription->visit->sponsor->category_name == 'NHIS',
                'payClass'          => $prescription->visit->sponsor->sponsorCategory->pay_class,
                'createdAt'         => (new Carbon($procedure->created_at))->format('d/m/y g:ia'),
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
