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
        return [
                'id'                    => $this->id,
                'name'                  => $this->name,
                'flag'                  => $this->flag,
                'resourceCategory'      => $this->resourceSubCategory->resourceCategory->id,
                'resourceCategoryId'    => $this->resourceSubCategory->resourceCategory->id,
                'resourceSubCategory'   => $this->resourceSubCategory->name,
                'resourceSubCategoryId' => $this->resourceSubCategory->id,
                'unit'                  => $this->unit_description,
                'purchasePrice'         => $this->purchase_price,
                'sellingPrice'          => $this->selling_price,
                'reOrder'               => $this->reorder_level,
                'expiryDate'            => (new Carbon($this->expiry_date))->format('Y-m-d'),
                // 'stockLevel'            => $this->stock_level,
        ];
    }
}
