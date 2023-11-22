<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Prescription;
use App\Models\Resource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PrescriptionService
{
    public function __construct(private readonly Prescription $prescription, private readonly Resource $resource)
    {
    }

    public function createFromDoctors(Request $data, User $user): Prescription
    {
        $bill = null;
        if ($data->quantity){
            $resource = $this->resource->find($data->resource);
            $bill = $resource->selling_price * $data->quantity;
        }

        return $user->prescriptions()->create([
            'resource_id'        => $data->resource,
            'prescription'       => $data->prescription,
            'consultation_id'    => $data->conId,
            'visit_id'           => $data->visitId,
            'qty_billed'         => $data->quantity,
            'bill'               => $bill,
            'bill_date'          => $bill ? new Carbon() : null,
            'note'               => $data->note
        ]);
    }

    public function getPaginatedInitialPrescriptions(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->prescription
                        ->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('prescription.resource.rescurceSubCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        // ->orWhereRelation('prescriptionSubCategory.prescriptionCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->prescription
                    ->where('consultation_id', $data->conId)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getInitialLoadTransformer(): callable
    {
       return  function (Prescription $prescription) {
            return [
                'id'                => $prescription->id,
                'prescribed'        => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                'resource'          => $prescription->resource->name,
                'prescription'      => $prescription->prescription,
                'quantity'          => $prescription->qty_billed,
                'by'                => $prescription->user->username,
                'note'              => $prescription->note
                // 'count'             => 0//$prescription->prescriptions()->count(),
            ];
         };
    }

    public function dataDifferenceInDays(string $date) {
                $now = Carbon::now();
                $carbonatedDate = new Carbon($date);
                $days = $now->diffInDays($carbonatedDate, false);

                if ($days >= 90){
                    return 'No';
                    }
               
                if ($days > 0 && $days < 90){
                    return 'Soon';
                    }

               if ($days <= 0){
                return 'Yes';
                    }
        
    }

    public function getFormattedList($data)
    {
        if (! empty($data->prescription)){
            return $this->prescription
                        ->where('name', 'LIKE', '%' . addcslashes($data->prescription, '%_') . '%' )
                        ->where('expiry_date', '>', new Carbon())
                        ->where('is_active', true)
                        ->orderBy('name', 'asc')
                        ->get();
        }
           
    }

    public function listTransformer()
    {
        return function (Prescription $prescription){
            return [
                'id' => $prescription->id,
                'name'  => $prescription->name.($prescription->flag ? ' '.$prescription->flag : '').($prescription->expiry_date && $prescription->expiry_date < (new Carbon())->addMonths(3) ? ' expiring soon - '.$prescription->expiry_date : '' )
            ];
        };
        
    }

    public function getPaginatedLabRequests(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        return $this->prescription
                    ->where('consultation_id', $data->conId)
                    ->whereRelation('resource.resourceSubCategory.resourceCategory', 'name', 'Laboratory')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getLabTransformer(): callable
    {
       return  function (Prescription $prescription) {
            return [
                'id'                => $prescription->id,
                'type'              => $prescription->resource->resourceSubCategory->name,
                'requested'         => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                'resource'          => $prescription->resource->name,
                'dr'                => $prescription->user->username,
                'result'            => $prescription->result,
                'sent'              => $prescription->result_date ?? '',
                'staff'             => $prescription->lab->name ?? '',
                'doc'               => $prescription->doc ?? '',

                // 'count'             => 0//$prescription->prescriptions()->count(),
            ];
         };
    }

    public function getPaginatedTreatmentRequests(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        return $this->prescription
                    ->where('consultation_id', $data->conId)
                    ->whereRelation('resource.resourceSubCategory.resourceCategory', 'name', 'Medication')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getTreatmentTransformer(): callable
    {
       return  function (Prescription $prescription) {
            return [
                'id'                => $prescription->id,
                'dr'                => $prescription->user->username,
                'category'          => $prescription->resource->resourceSubCategory->name,
                'resource'          => $prescription->resource->name,
                'prescription'      => $prescription->prescription,
                'prescribed'        => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                'billed'            => $prescription->bill_date ? (new Carbon($prescription->bill_date))->format('d/m/y g:ia') : '',
                // 'count'             => 0//$prescription->prescriptions()->count(),
            ];
         };
    }
}