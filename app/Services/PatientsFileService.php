<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\PatientsFile;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientsFileService
{
    public function __construct(private readonly PatientsFile $patientsFile)
    {
    }

    public function create(Request $data, Visit $visit, User $user): PatientsFile
    {
        $file = $data->file('patientsFile');

        $extension = $file->extension();

        $mimeType = $file->getClientMimeType();

        $storageFilename = bin2hex(random_bytes(10));

        $file->storeAs('patients', $storageFilename);

        return $user->patientsFiles()->create([
            'filename'          => $data->filename,
            'storage_filename'  => $storageFilename,
            'client_mimetype'   => $mimeType,
            'extension'         => $extension,
            'third_party_id'    => $data->thirdParty,
            'visit_id'          => $visit->id,
            'patient_id'        => $visit->patient_id,
            'comment'           => $data->comment,
        ]);
    }

    public function getPaginatedPatientsFile(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query      = $this->patientsFile->select('id', 'visit_id', 'third_party_id', 'user_id', 'created_at', 'comment', 'filename')
                        ->with([
                            'user:id,username',
                            'visit:id,closed',
                            'thirdParty:id,short_name'
                        ])
                        ->where('visit_id', $data->visitId);

        if (! empty($params->searchTerm)) {
            return $query->where('filename', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (PatientsFile $patientsFile) {
            return [
                'id'                => $patientsFile->id,
                'createdAt'         => (new Carbon($patientsFile->created_at))->format('d/m/Y g:ia'),
                'filename'          => $patientsFile->filename,
                'thirdParty'        => $patientsFile->thirdParty?->short_name,
                'comment'           => $patientsFile->comment,
                'uploadedBy'        => $patientsFile->user->username,
                'closed'            => $patientsFile->visit->closed,
            ];
         };
    }

    public function findFile(PatientsFile $patientsFile)
    {
        $storageFilename    = $patientsFile->storage_filename;
        $filename           = $patientsFile->filename;
        $extension          = $patientsFile->extension;
        $thirdParty          = $patientsFile->thirdParty?->short_name;
        $patient            = $patientsFile->visit->patient->patientId();
        $name = $filename. ' ' .($thirdParty ?? ''). ' - '. str_replace('/', ' ', $patient).'.'.$extension;
       
        return  Storage::download('patients/'.$storageFilename, $name, ['Content-Type' => 'application/pdf']);

    }

    public function processDeletion(PatientsFile $patientsFile)
    {
        $storageFilename    = $patientsFile->storage_filename;

        if (Storage::exists('patients/'.$storageFilename)){
            Storage::delete('patients/'.$storageFilename);
            return $patientsFile->destroy($patientsFile->id);
        }
        return response("We couldn't find this file", 222);
    }
}
