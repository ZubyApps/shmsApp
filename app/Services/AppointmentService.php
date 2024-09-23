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

        if (! empty($params->searchTerm)) {
            return $this->appointment
                        ->whereBetween('date', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
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
            return $this->appointment
            ->where('doctor_id', '=', $user->id)
            ->orderBy($orderBy, $orderDir)
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->appointment
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (Appointment $appointment) {
            $latestVisit = $appointment->patient->latestVisit;
            return [
                'id'                => $appointment->id,
                'createdAt'         => (new Carbon($appointment?->created_at))->format('d/m/Y'),
                'patient'           => $appointment->patient->patientId(),
                'phone'             => $appointment->patient->phone,
                'sponsor'           => $appointment->patient->sponsor->name . ' - ' . $appointment->patient->sponsor->category_name,
                'lastVisitDate'     => (new Carbon($latestVisit?->created_at))->format('d/m/Y g:ia'),
                'lastDiagnosis'     => $latestVisit?->consultations->sortDesc()->first()?->provisional_diagnosis,
                'doctor'            => $appointment->doctor->username,
                'date'              => (new Carbon($appointment->date))->format('d/m/Y g:ia'),
                'createdBy'         => $appointment->user->username,
                'rawDateTime'       => $appointment->date,
                'flagSponsor'       => $appointment->patient->sponsor->flag,
                'flagPatient'       => $appointment->patient->flag,
                'flagReason'        => $appointment->patient->flag_reason,
            ];
         };
    }
}
