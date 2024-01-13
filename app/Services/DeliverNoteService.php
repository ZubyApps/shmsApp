<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\DeliveryNote;
use App\Models\User;
use Illuminate\Http\Request;

Class DeliveryNoteService
{
    public function __construct(private readonly DeliveryNote $deliveryNote)
    {
        
    }

    public function create(Request $data, User $user): DeliveryNote
    {
       $deliveryNote = $user->deliveryNote->update([
            'date'                  => $data->date,
            'date_of_admission'     => $data->dateOfAdmission,
            'date_of_delivery'      => $data->dateOfDelivery,
            'apgar_score'           => $data->apgarScore,
            'birth_weight'          => $data->birthWeight,
            'mode_of_delivery'      => $data->modeOfDelivery,
            'length_of_parity'      => $data->lengthOfParity,
            'head_circumference'    => $data->headCircumference,
            'sex'                   => $data->sex,
            'ebl'                   => $data->ebl,
            'con_id'                => $data->conId
        ]);

        return $deliveryNote;
    }

    public function update(Request $data, DeliveryNote $deliveryNote, User $user): DeliveryNote
    {
        $deliveryNote->update([
            'date'                  => $data->date,
            'date_of_admission'     => $data->dateOfAdmission,
            'date_of_delivery'      => $data->dateOfDelivery,
            'apgar_score'           => $data->apgarScore,
            'birth_weight'          => $data->birthWeight,
            'mode_of_delivery'      => $data->modeOfDelivery,
            'length_of_parity'      => $data->lengthOfParity,
            'head_circumference'    => $data->headCircumference,
            'sex'                   => $data->sex,
            'ebl'                   => $data->ebl,
            'con_id'                => $data->conId,
            'user_id'               => $user->id
        ]);

        return $deliveryNote;
    }
}