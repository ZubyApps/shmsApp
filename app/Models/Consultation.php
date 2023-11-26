<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function visit() 
    {
        return $this->belongsTo(Visit::class);
    }

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function prescriptions() 
    {
        return $this->hasMany(Prescription::class);
    }
}
