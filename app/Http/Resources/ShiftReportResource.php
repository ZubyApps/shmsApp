<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShiftReportResource extends JsonResource
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
            'shift'         => $this->shift,
            'report'        => $this->report,
            'writtenBy'     => $this->user->username,
            'writtenAt'     => (new Carbon($this->created_at))->format('g:ia D d/m/Y')
        ];
    }
}
