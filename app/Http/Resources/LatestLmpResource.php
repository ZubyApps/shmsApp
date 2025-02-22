<?php

namespace App\Http\Resources;

use App\Models\Consultation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LatestLmpResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lmp = Consultation::where('visit_id', $this->id)->whereNotNull('lmp')->orderBy('id', 'desc')->first()?->lmp;
        return [
            'lmp' => $lmp ? Carbon::parse($lmp)->format('Y-m-d') : null
        ];
    }
}
