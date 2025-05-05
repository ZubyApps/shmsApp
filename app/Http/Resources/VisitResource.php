<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'ancRegId'      => $this->antenatalRegisteration?->id,
            'came'          => (new Carbon($this->created_at))->format('D jS M Y - g:ia'),
            'consultations' => new ConsultationReviewCollection($this->consultations),
            'visitType'     => $this->visit_type,
        ];
    }
}
