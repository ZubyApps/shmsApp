<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\MedicalReport;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;

Class MedicalReportService
{
    public function __construct(private readonly MedicalReport $medicalReport)
    {
        
    }

    public function create(Request $data, User $user): MedicalReport
    {
       $medicalReport = $user->medicalReports()->create([
            'doctor'                => $data->doctor,
            'designation'           => $data->designation,
            'type'                  => $data->type,
            'requested_by'          => $data->requestedBy,
            'recipients_address'    => $data->recipientsAddress,
            'report'                => $data->report,
            'visit_id'              => $data->visitId,
            'patient_id'            => $data->patientId,
        ]);

        return $medicalReport;
    }

    public function update(Request $data, MedicalReport $medicalReport, User $user): MedicalReport
    {
        $medicalReport->update([
            'doctor'                => $data->doctor,
            'designation'           => $data->designation,
            'type'                  => $data->type,
            'recipients_address'    => $data->recipientsAddress,
            'report'                => $data->report,
            'user_id'               => $user->id,
        ]);

        return $medicalReport;
    }

    public function getMedicalReports(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->medicalReport
                        ->where('type', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('doctor', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->medicalReport
                    ->where('visit_id', $data->visitId)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getMedicalReportTransformer(): callable
    {
       return  function (MedicalReport $medicalReport) {
            return [
                'id'                => $medicalReport->id,
                'date'              => (new Carbon($medicalReport->date))->format('d/m/y'),
                'doctor'            => $medicalReport->doctor,
                'designation'       => $medicalReport->designation,
                'type'              => $medicalReport->type,
                'requestedBy'       => $medicalReport->requested_by,
                'recipientsAddress' => $medicalReport->recipients_address,
                'creator'           => $medicalReport->user->username,
                'closed'            => $medicalReport->visit->closed
            ];
         };
    }
}