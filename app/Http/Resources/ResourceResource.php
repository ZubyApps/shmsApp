<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($request->routeIs('Addstock')) {
            return [
                'id'       => $this->id,
                'resource' => $this->name,
                'hmsStock' => $this->stock_level,
            ];
        }
        return [
                'id'                    => $this->id,
                'name'                  => $this->name,
                'flag'                  => $this->flag,
                // 'markedFor'             => $this->marked_for,
                'markedFor'             => $this->markedFor?->id,
                'resourceCategory'      => $this->resourceSubCategory->resourceCategory->id,
                'resourceCategoryId'    => $this->resourceSubCategory->resourceCategory->id,
                'resourceSubCategory'   => $this->resourceSubCategory->name,
                'resourceSubCategoryId' => $this->resourceSubCategory->id,
                'medicationCategory'    => $this->medicationCategory?->name,
                'medicationCategoryId'  => $this->medicationCategory?->id,
                // 'unitDescription'       => $this->unit_description,
                'unitDescription'      => $this->unitDescription?->id,
                'purchasePrice'         => $this->purchase_price,
                'sellingPrice'          => $this->selling_price,
                'reOrder'               => $this->reorder_level,
                'expiryDate'            => $this->expiry_date ? (new Carbon($this->expiry_date))->format('Y-m') : null,
        ];
    }
}
