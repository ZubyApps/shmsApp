<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Consultation;
use App\Models\User;
use App\Models\Visit;
use App\Models\Ward;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WardService
{
    public function __construct(private readonly Ward $ward)
    {
    }

    public function create(Request $data, User $user): Ward
    {
        return $user->wards()->create([
            'short_name'    => $data->shortName,
            'long_name'     => $data->longName,
            'bed_number'    => $data->bedNumber,
            'description'   => $data->description,
            'bill'          => $data->bill,
        ]);
    }

    public function update(Request $data, Ward $ward, User $user): Ward
    {
       $ward->update([
            'short_name'   => $data->shortName,
            'long_name'    => $data->longName,
            'bed_number'   => $data->bedNumber,
            'description'   => $data->description,
            'bill'         => $data->bill,
            'flag'         => $data->flag,
            'flag_reason'  => $data->flagReason,
            'user_id'      => $user->id,
        ]);

        return $ward;
    }

    public function clearWard(Ward $ward)
    {
        return $ward->update(['visit_id' => null]);
    }

    public function getPaginatedWards(DataTableQueryParams $params)
    {
        $orderBy    = 'long_name';
        $orderDir   =  'asc';
        $query      = $this->ward->select('id', 'user_id', 'visit_id', 'short_name', 'long_name', 'bill', 'bed_number', 'description', 'bill', 'flag', 'flag_reason', 'created_at')
                        ->with(['user:id,username'])
                        ->withExists('visit as hasVisits');

        if (! empty($params->searchTerm)) {
            return $query->where('short_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('long_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (Ward $ward) {
            return [
                'id'                => $ward->id,
                'shortName'         => $ward->short_name,
                'longName'          => $ward->long_name,
                'bedNumber'         => $ward->bed_number,
                'description'       => $ward->description,
                'bill'              => $ward->bill,
                'flag'              => $ward->flag,
                'flagReason'        => $ward->flag_reason,
                'createdBy'         => $ward->user->username,
                'createdAt'         => (new Carbon($ward->created_at))->format('d/m/Y'),
                'occupied'          => $ward->visit_id ? 'Yes' : 'No',
                'count'             => $ward->hasVisits,//visit()->count()
            ];
         };
    }

    public function getFormattedList($data)
    {
            return $this->ward->select('id', 'visit_id', 'long_name', 'short_name', 'bed_number', 'flag', 'flag_reason')
                        ->with(['visit' => function($query){
                            $query->select('id', 'patient_id')
                                ->with(['patient:id,first_name,middle_name,last_name,card_no']);
                        }])
                        ->orderBy('long_name', 'asc')
                        ->get();
           
    }

    public function listTransformer()
    {
        return function (Ward $ward){
            return [
                'id'            => $ward->id,
                'display'       => $ward->long_name . ' (' . $ward->short_name . ') - Bed' . $ward->bed_number,
                'occupant'      => $ward->visit?->patient?->patientId(),
                'flag'          => $ward->flag,
                'flagReason'    => $ward->flag_reason,
            ];
        };   
    }

    public function updateAllWards(Ward $ward)
    {
        $visits         = Visit::where('ward', $ward->short_name)->where('bed_no', 'Bed'.$ward->bed_number)->get();
        $consultations  = Consultation::where('ward', $ward->short_name)->where('bed_no', 'Bed'.$ward->bed_number)->get();
        $alls            = [...$visits, ...$consultations];
        foreach($alls as $all){
            $all->update(['ward' => $ward->id]);
        }

        return $visits;
    }
}
