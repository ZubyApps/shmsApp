<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function dispenseResources() 
    {
        return $this->hasMany(DispenseResource::class);
    }

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

    public function nameWithIndicators()
    {
        return $this->name.($this->flag ? ' - '.$this->flag : '').($this->expiry_date && $this->expiry_date < (new Carbon())->addMonths(3) ? ' - expiring soon - '.(new Carbon($this->expiry_date))->format('d/M/y') : '' );
    }
}
