<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use App\DataObjects\DataTableQueryParams;
use App\Models\Partograph;
use Carbon\Carbon;

Class PartographService
{
    public function __construct(private readonly Partograph $partograph)
    {
        
    }

    public function create(Request $data, User $user): Partograph
    {
       $partograph = $user->partographs()->create([
            'labour_record_id' => $data->labourRecordId,
            'recorded_at' => $data->recordedAt ?? (new Carbon())->format('Y-m-d\TH:i:s'),
            'parameter_type' => $data->parameterType ,
            'value' => $data->value,
        ]);

        return $partograph;
    }

    public function update(Request $data, Partograph $partograph, User $user): Partograph
    {
        $partograph->update([
            'recorded_at' => $data->recordedAtRaw ?? $partograph->recorded_at,
            'parameter_type' => $partograph->parameter_type,
            'value' => $data->value ?? $partograph->value,
            'updated_by' => $user->id,           
        ]);

        return $partograph;
    }

    public function getPartographData(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'recorded_at';
        $orderDir   =  'asc';

        return $this->partograph->select('id', 'labour_record_id', 'user_id', 'updated_by', 'recorded_at', 'parameter_type', 'value', 'created_at', 'updated_at')
                ->with([
                        'user:id,username',
                        'labourRecord:id',
                        'updatedBy:id,username'
                    ])
                    ->where('labour_record_id', $data->labourRecordId)
                    ->where('parameter_type', $data->parameterType)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getPartographChartData($data)
    {
        $orderBy    = 'recorded_at';
        $orderDir   =  'asc';

        return $this->partograph->select('id', 'labour_record_id', 'user_id', 'updated_by', 'recorded_at', 'parameter_type', 'value', 'created_at', 'updated_at')
                        ->with([
                                'user:id,username',
                                'labourRecord:id',
                                'updatedBy:id,username'
                            ])
                    ->where('labour_record_id', $data->labourRecordId)
                    ->orderBy($orderBy, $orderDir)
                    ->get();
    }

    public function getPartographDataTransformer(): callable
    {
       return  function (Partograph $partograph) {
            return [
                'id'                => $partograph->id,
                'labourRecordId'    => $partograph->labourRecord->id,
                'recordedAt'        => $partograph->recorded_at ? $partograph->recorded_at->format('d/m/y g:ia') : '',
                'recordedAtRaw'     => $partograph->recorded_at ? $partograph->recorded_at->format('Y-m-d\TH:i:s') : '',
                'recordedBy'        => $partograph->user?->username,
                'parameterType'     => $partograph->parameter_type,
                'value'             => $partograph->value,
                'createdAt'         => $partograph->created_at->format('d/m/y g:ia'),
                'updatedAt'         => $partograph->update_at ? $partograph->update_at->format('d/m/y g:ia') : '',
                'updateBy'          => $partograph->updatedBy?->username,
            ];
         };
    }
}