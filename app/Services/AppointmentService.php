<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AppointmentService
{
    public function __construct(private readonly Appointment $appointment)
    {
    }

    public function create(Request $data, Patient $patient, User $user): Appointment
    {
        return $user->appointments()->create([
            'date'                  => $data->date,
            'doctor_id'             => $data->doctor,
            'remarks'               => $data->remarks,
            'patient_id'            => $patient->id,
        ]);
    }

    public function getPaginatedAppointments(DataTableQueryParams $params, $data, User $user)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';
        $query = $this->appointment->select('id', 'patient_id', 'user_id', 'doctor_id', 'date', 'created_at')->with([
            'patient' => function ($query){
                $query->select('id', 'sponsor_id', 'first_name', 'middle_name', 'last_name', 'card_no', 'phone', 'flag', 'flag_reason', 'flagged_by', 'flagged_at')
                    ->with([
                        'flaggedBy:id,username',
                        'sponsor:id,name,category_name,flag',
                        'latestVisit' => function ($query) {
                            $query->select('id', 'visits.patient_id', 'created_at')
                            ->with(['latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment']);
                        }
                    ]);
            }, 
            'user:id,username', 
            'doctor:id,username',
        ]);

        if (! empty($params->searchTerm)) {
            return $query->whereBetween('date', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
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

        if ($data->filterBy == 'My Appointments'){
            return $query->where('doctor_id', '=', $user->id)
            ->orderBy($orderBy, $orderDir)
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (Appointment $appointment) {
            $latestVisit = $appointment->patient?->latestVisit;
            return [
                'id'                => $appointment->id,
                'createdAt'         => (new Carbon($appointment?->created_at))->format('d/m/Y'),
                'patient'           => $appointment->patient->patientId(),
                'phone'             => $appointment->patient->phone,
                'sponsor'           => $appointment->patient->sponsor->name . ' - ' . $appointment->patient->sponsor->category_name,
                'lastVisitDate'     => (new Carbon($latestVisit?->created_at))->format('d/m/Y g:ia'),
                'lastDiagnosis'     => $latestVisit->latestConsultation?->icd11_diagnosis ?? $latestVisit->latestConsultation?->provisional_diagnosis ?? $latestVisit->latestConsultation?->assessment,
                'doctor'            => $appointment->doctor->username,
                'date'              => (new Carbon($appointment->date))->format('d/m/Y g:ia'),
                'createdBy'         => $appointment->user->username,
                'rawDateTime'       => $appointment->date,
                'flagSponsor'       => $appointment->patient->sponsor->flag,
                'flagPatient'       => $appointment->patient->flag,
                'flagReason'        => $appointment->patient->flag_reason,
                'flaggedBy'         => $appointment->patient->flaggedBy?->username,
                'flaggedAt'         => $appointment->patient->flagged_at ? (new Carbon($appointment->patient->flagged_at))->format('d/m/y g:ia') : '',
            ];
         };
    }
}
