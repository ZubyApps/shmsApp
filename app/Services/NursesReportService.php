<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\NursesReport;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;

Class NursesReportService
{
    public function __construct(private readonly NursesReport $nursesReport)
    {
        
    }

    public function create(Request $data, Visit $visit, User $user): NursesReport
    {
       $nursesReport = $user->nursesReports()->create([
            'report'       => $data->report,
            'visit_id'     => $visit->id,
            'patient_id'   => $visit->patient->id,
            'user_id'      => $user->id
        ]);

        return $nursesReport;
    }

    public function update(Request $data, NursesReport $nursesReport, User $user): NursesReport
    {
       $nursesReport->update([
            'report'       => $data->report,
            'user_id'      => $user->id
        ]);

        return $nursesReport;
    }

    public function getNursesReports(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->nursesReport
                        ->where('visit_id', $data->visitId)
                        ->whereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->nursesReport
                    ->where('visit_id', $data->visitId)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getNursesReportTransformer(): callable
    {
       return  function (NursesReport $nursesReport) {
            return [
                'id'                => $nursesReport->id,
                'date'              => (new Carbon($nursesReport->created_at))->format('D d/m/y g:ia'),
                'report'            => $nursesReport->report,
                'writtenBy'         => $nursesReport->user->username,
                'closed'            => $nursesReport->visit->closed
            ];
         };
    }
}