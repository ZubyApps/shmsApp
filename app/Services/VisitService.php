<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VisitService
{
    public function __construct(private readonly Visit $visit)
    {
    }

    public function create(Request $data, User $user): Visit
    {
        return $user->visits()->create([
                "patient_id"          => $data->patientId,
        ]);
    }

    public function update(Request $data, Visit $visit, User $user): Visit
    {
       $visit->update([
                "patient_type"          => $data->patientType,
        ]);

        return $visit;
    }

    public function getPaginatedPatients(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->visit
                        ->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        // ->orWhere('middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        // ->orWhere('phone', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        // ->orWhereRelation('sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        // ->orWhereRelation('sponsor.sponsorCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'patientId'         => $visit->patient->id,
                'patient'           => $visit->patient->card_no.' ' .$visit->patient->first_name.' '. $visit->patient->middle_name.' '.$visit->patient->last_name,
                'sex'               => $visit->patient->sex,
                'age'               => (new Carbon($visit->patient->date_of_birth))->age.'yrs',
                'sponsor'           => $visit->patient->sponsor->name,
                'came'              => (new Carbon($visit->created_at))->diffForHumans(),
                'doctor'            => $visit->doctor ?? '',
                'patientType'       => $visit->patient->patient_type,
                'status'            => $visit->status
            ];
         };
    }

    public function initiateConsultation(Visit $visit, User $user) {
        $visit->update([
            'doctor'    =>  $user->username
        ]);
    }
}