<?php

namespace App\Models;

use App\Enum\VerificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $cast = [
        'verification_status' => VerificationStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function sponsor()
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function consultations() 
    {
        return $this->hasMany(Consultation::class);
    }

    public function vitalSigns() 
    {
        return $this->hasMany(VitalSigns::class);
    }

    public function prescriptions() 
    {
        return $this->hasMany(Prescription::class);
    }

    public function medicationCharts() 
    {
        return $this->hasMany(MedicationChart::class);
    }
}
