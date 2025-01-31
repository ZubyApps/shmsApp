<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\ShiftPerformance;
use App\Models\ShiftReport;
use App\Models\User;
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
        $query = $this->shiftReport::with([
            'user',
            'viewedBy',
            'viewedBy1',
            'viewedBy2',
        ]);

        if (! empty($params->searchTerm)) {
            return $query->where('department', $data->department)
                        ->whereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->where('department', $data->department)
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
                'viewedAt1'         => $shiftReport->viewed_at_1 ? (new Carbon($shiftReport->viewed_at_1))->format('D d/M/y g:ia') : '',
                'viewedAt2'         => $shiftReport->viewed_at_2 ? (new Carbon($shiftReport->viewed_at_2))->format('D d/M/y g:ia') : '',
                'viewedBy'          => $shiftReport->viewedBy?->username,
                'viewedBy1'         => $shiftReport->viewedBy1?->username,
                'viewedBy2'         => $shiftReport->viewedBy2?->username,
                'viewedShift'       => $shiftReport->viewed_shift,
                'viewedShift1'      => $shiftReport->viewed_shift_1,
                'viewedShift2'      => $shiftReport->viewed_shift_2,
                'notify'            => $shiftReport->notify,
                'writtenById'       => $shiftReport->user->id,
                'userId'            => request()->user()->id,
            ];
         };
    }

    public function mark(ShiftReport $shiftReport, User $user)
    {
        if ($shiftReport->department == 'nurses'){
            $shiftPerformance = ShiftPerformance::where('department', 'Nurse')->where('is_closed', false)->orderBy('id', 'desc')->first();

            if (!$shiftReport->viewed_at && $shiftReport->notify){
                return $shiftReport->update([
                    'viewed_at'     => new Carbon(),
                    'viewed_by'     => $user->id,
                    'viewed_shift'  => $shiftPerformance->shift,
                    'notify'        => false
                    ]);
            }

            if (!$shiftReport->viewed_at_1 && $shiftReport->notify){
                return $shiftReport->update([
                    'viewed_at_1'       => new Carbon(),
                    'viewed_by_1'       => $user->id,
                    'viewed_shift_1'    => $shiftPerformance->shift,
                    'notify'            => false
                    ]);
            }

            if (!$shiftReport->viewed_at_2 && $shiftReport->notify){
                return  $shiftReport->update([
                    'viewed_at_2'       => new Carbon(),
                    'viewed_by_2'       => $user->id,
                    'viewed_shift_2'    => $shiftPerformance->shift,
                    'notify'            => false
                    ]);
            }     
        }

        if (!$shiftReport->viewed_at){
            return $shiftReport->update([
                'viewed_at' => new Carbon(),
                'viewed_by' => $user->id
                ]);
        }  
    }
}