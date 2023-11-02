<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceCategory extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function resourceSubCategories() 
    {
        return $this->hasMany(ResourceSubCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
