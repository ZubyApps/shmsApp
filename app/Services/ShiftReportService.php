<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\NursesReport;
use App\Models\ShiftReport;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;

Class ShiftReportService
{
    public function __construct(private readonly ShiftReport $shiftReport)
    {
        
    }

    public function create(Request $data, User $user): ShiftReport
    {
       $shiftReport = $user->shiftReports()->create([
            'department'   => $data->department,
            'shift'        => $data->shift,
            'report'       => $data->report,
            'user_id'      => $user->id
        ]);

        return $shiftReport;
    }

    public function update(Request $data, ShiftReport $shiftReport, User $user)
    {
       return $shiftReport->update([
            'shift'        => $data->shift,
            'report'       => $data->report,
            'user_id'      => $user->id
        ]);
    }

    public function getShiftReports(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->shiftReport
                        ->where('department', $data->department)
                        ->whereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->shiftReport
                    ->where('department', $data->department)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getShiftReportTransformer(): callable
    {
       return  function (ShiftReport $shiftReport) {
            return [
                'id'                => $shiftReport->id,
                'date'              => (new Carbon($shiftReport->created_at))->format('D d/m/y g:ia'),
                'shift'             => $shiftReport->shift,
                'report'            => $shiftReport->report,
                'writtenBy'         => $shiftReport->user->username,
                'viewedAt'          => $shiftReport->viewed_at ? (new Carbon($shiftReport->viewed_at))->format('D d/M/y g:ia') : '',
                'viewedBy'          => $shiftReport->viewedBy?->username,
                'writtenById'       => $shiftReport->user->id,
                'userId'            => request()->user()->id,
            ];
         };
    }

    public function mark(ShiftReport $shiftReport, User $user)
    {
        if ($shiftReport->viewed_at){
            
        } else {
            $shiftReport->update([
            'viewed_at' => new Carbon(),
            'viewed_by' => $user->id
            ]);
        }
    }
}