<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function addResources() 
    {
        return $this->hasMany(AddResourceStock::class);
    }

    public function prescriptions() 
    {
        return $this->hasMany(Prescription::class);
    }

    public function resourceSubCategory()
    {
        return $this->belongsTo(ResourceSubCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resourceSupplier()
    {
        return $this->belongsTo(ResourceSupplier::class);
    }

    public function bulkRequests() 
    {
        return $this->hasMany(BulkRequest::class);
    }

    public function medicationCategory()
    {
        return $this->belongsTo(MedicationCategory::class);
    }

    public function markedFor()
    {
        return $this->belongsTo(MarkedFor::class);
    }

    public function unitDescription()
    {
        return $this->belongsTo(UnitDescription::class);
    }

    public function nameWithIndicators()
    {
         if ($this->category == 'Medications' || $this->category == 'Consumables'){
            return $this->name.$this->expiryDateChecker($this->expiry_date).$this->stockLevelChecker($this);
        }

        return $this->name;
    }

    public function expiryDateChecker($expiryDate)
    { 
        if ($expiryDate && $expiryDate <= new Carbon()){
            return ' - expired - '.(new Carbon($this->expiry_date))->format('d/M/y');
        }
        if ($expiryDate && $expiryDate < (new Carbon())->addMonths(3)){
            return ' - expiring soon - '.(new Carbon($this->expiry_date))->format('d/M/y');
        }

    }

    public function stockLevelChecker($resource)
    {
        if ($resource->stock_level < $resource->reorder_level){
            return ' - '.$resource->stock_level.' '.$resource->unitDescription?->short_name.' left'.' - reorder';
        }

        if ($resource->stock_level < 1){
            return '- Not in stock';
        }

        return ' - '.$resource->stock_level.' '.$resource->unitDescription?->short_name.' left';
    }

    public function sponsors()
    {
        return $this->belongsToMany(Sponsor::class)
                    ->using(ResourceSponsor::class)
                    ->withPivot('selling_price', 'user_id')
                    ->withTimestamps();
    }

    public function getSellingPriceForSponsor(?Sponsor $sponsor = null): int
    {
        if ($sponsor) {
            $sponsorPrice = $this->sponsors()
                ->where('sponsor_id', $sponsor->id)
                ->first()
                ?->pivot
                ?->selling_price;

            return $sponsorPrice ?? $this->selling_price ?? 0;
        }

        return $this->selling_price ?? 0;
    }
}
