<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\DeliveryNote;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

Class DeliveryNoteService
{
    public function __construct(private readonly DeliveryNote $deliveryNote)
    {
        
    }

    public function create(Request $data, User $user): DeliveryNote
    {
       $deliveryNote = $user->deliveryNotes()->create([
            'date'                  => $data->date,
            'time_of_admission'     => $data->timeOfAdmission,
            'time_of_delivery'      => $data->timeOfDelivery,
            'apgar_score'           => $data->apgarScore,
            'birth_weight'          => $data->birthWeight,
            'mode_of_delivery'      => $data->modeOfDelivery,
            'length_of_parity'      => $data->lengthOfParity,
            'head_circumference'    => $data->headCircumference,
            'sex'                   => $data->sex,
            'ebl'                   => $data->ebl,
            'note'                  => $data->note,
            'consultation_id'       => $data->conId,
            'visit_id'              => $data->visitId
        ]);

        return $deliveryNote;
    }

    public function update(Request $data, DeliveryNote $deliveryNote, User $user): DeliveryNote
    {
        $deliveryNote->update([
            'date'                  => $data->date,
            'time_of_admission'     => $data->timeOfAdmission,
            'time_of_delivery'      => $data->timeOfDelivery,
            'apgar_score'           => $data->apgarScore,
            'birth_weight'          => $data->birthWeight,
            'mode_of_delivery'      => $data->modeOfDelivery,
            'length_of_parity'      => $data->lengthOfParity,
            'head_circumference'    => $data->headCircumference,
            'sex'                   => $data->sex,
            'ebl'                   => $data->ebl,
            'note'                  => $data->note,
            'user_id'               => $user->id
        ]);

        return $deliveryNote;
    }

    public function getDeliveryNotes(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->deliveryNote
                        ->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->deliveryNote
                    ->where('consultation_id', $data->conId)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getDeliveryNoteTransformer(): callable
    {
       return  function (DeliveryNote $deliveryNote) {
            return [
                'id'                => $deliveryNote->id,
                'date'              => (new Carbon($deliveryNote->date))->format('d/m/y'),
                'timeAdmitted'      => (new Carbon($deliveryNote->time_of_admission))->format('d/m/y g:ia'),
                'timeDelivered'     => (new Carbon($deliveryNote->time_of_delivery))->format('d/m/y g:ia'),
                'modeOfDelivery'    => $deliveryNote->mode_of_delivery,
                'ebl'               => $deliveryNote->ebl,
                'note'              => $deliveryNote->note,
                'nurse'             => $deliveryNote->user->username,
                'closed'            => $deliveryNote->visit->closed
            ];
         };
    }
}