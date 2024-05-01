<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationCategory extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function resources() 
    {
        return $this->hasMany(Resource::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
