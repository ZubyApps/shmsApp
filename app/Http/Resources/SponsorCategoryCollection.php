<?php

namespace App\Http\Resources;

use App\Models\SponsorCategory;
use App\Services\RequestService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SponsorCategoryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $param = (new RequestService)->getDataTableQueryParameters($request);
    
        return [
            'data' => $this->collection,
            'draw' => $param->draw,
            'recordsTotal' => SponsorCategory::count(),
            'recordsFiltered' => count($this->collection)
            ];
    }
}
