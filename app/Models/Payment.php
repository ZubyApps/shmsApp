<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
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

    public function payMethod() 
    {
        return $this->belongsTo(PayMethod::class);
    }

    public function patient() 
    {
        return $this->belongsTo(Patient::class);
    }

    public function prescriptions() 
    {
        return $this->hasMany(Prescription::class);
    }

    public function walkIn() 
    {
        return $this->belongsTo(WalkIn::class);
    }

    public function mortuaryService() 
    {
        return $this->belongsTo(MortuaryService::class);
    }
}
