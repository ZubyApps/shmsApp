<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SponsorCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'description'       => $this->description,
            'consultationFee'   => $this->consultation_fee,
            'payClass'          => $this->pay_class,
            'approval'          => $this->approval === 0 ? 'false' : 'true',
            'billMatrix'        => $this->bill_matrix,
            'balanceRequired'   => $this->balance_required === 0 ? 'false' : 'true',
            'createdAt'         => Carbon::parse($this->created_at)->format('d/m/Y')
        ];
    }
}
