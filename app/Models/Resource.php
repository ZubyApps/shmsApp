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

    public function nameWithIndicators()
    {
        return $this->name.$this->expiryDateChecker($this->expiry_date).$this->stockLevelChecker($this);
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
            return ' - '.$resource->stock_level.' '.$resource->unit_description.' left'.' - reorder';
        }

        if ($resource->stock_level < 1){
            return '- Not in stock';
        }

        return ' - '.$resource->stock_level.' '.$resource->unit_description.' left';
    }
}
