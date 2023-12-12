<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function labScientist()
    {
        return $this->belongsTo(User::class, 'lab_id');
    }

    public function medicationCharts() 
    {
        return $this->hasMany(MedicationChart::class);
    }
}
