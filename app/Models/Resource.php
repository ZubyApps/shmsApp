<?php

namespace App\Models;

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
        return $this->hasMany(AddResource::class);
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
}
