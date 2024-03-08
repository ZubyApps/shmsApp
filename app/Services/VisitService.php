<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Consultation;
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

    public function getVisitSummaryBySponsor(DataTableQueryParams $params, $data)
    {
        $current = Carbon::now();

        if (! empty($params->searchTerm)) {
            return DB::table('visits')
            ->selectRaw('sponsors.name as sponsor, sponsors.id as id, sponsor_categories.name as category, COUNT(patients.id) as patientsCount')
            ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
            ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
            ->leftJoin('patients', 'visits.patient_id', '=', 'patients.id')
            ->where('sponsors.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
            ->whereMonth('visits.created_at', $current->month)
            ->whereYear('visits.created_at', $current->year)
            ->groupBy('sponsor')
            ->orderBy('sponsor')
            ->orderBy('patientsCount')
            ->get()
            ->toArray();
        }

        return DB::table('visits')
            ->selectRaw('sponsors.name as sponsor, COUNT(patients.id) as patientsCount, SUM(CASE WHEN admission_status = "Outpatient" THEN 1 ELSE 0 END) AS outpatients, SUM(CASE WHEN admission_status = "Inpatient" THEN 1 ELSE 0 END) AS inpatients, SUM(CASE WHEN admission_status = "Observation" THEN 1 ELSE 0 END) AS observations')
            ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
            ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
            ->leftJoin('patients', 'visits.patient_id', '=', 'patients.id')
            ->where('consulted', '!=', null)
            ->whereMonth('visits.created_at', $current->month)
            ->whereYear('visits.created_at', $current->year)
            ->groupBy('sponsor')
            ->orderBy('sponsor')
            ->orderBy('patientsCount')
            ->get()
            ->toArray();
    }

    public function getVisits(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';
        $current = Carbon::now();

        if (! empty($params->searchTerm)) {
            return $this->visit
                        ->Where('consulted', '!=', null)
                        ->whereDay('created_at', $current->yesterday())
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
                    ->Where('consulted', '!=', null)
                    ->whereDay('created_at', $current->today())
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getVisitsTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->created_at))->format('d/M/y g:ia'),
                'seen'              => (new Carbon($visit->consulted))->format('d/M/y g:ia'),
                'patientType'       => explode(".", $visit->patient->patient_type)[0],
                'doctor'            => $visit->doctor->username ?? '',
                'ancRegId'          => $visit->antenatalRegisteration?->id,
                'patient'           => $visit->patient->patientId(),
                'phone'             => $visit->patient->phone,
                'address'           => $visit->patient->address,
                'state'             => $visit->patient->state_of_residence,
                'sex'               => $visit->patient->sex,
                'age'               => $visit->patient->age(),
                'nok'               => $visit->patient->next_of_kin,
                'nokPhone'          => $visit->patient->next_of_kin_phone,
                'sponsor'           => $visit->sponsor->name,
                'sponsorCategory'   => $visit->sponsor->category_name,
                'status'            => $visit->admission_status,
                'diagnosis'         => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->icd11_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->provisional_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->assessment,
                'ancCount'          => explode(".", $visit->patient->patient_type)[0] == 'ANC' ? $visit->consultations->count() : '',
                'closed'            => true//$visit->closed,
            ];
         };
    }
}
