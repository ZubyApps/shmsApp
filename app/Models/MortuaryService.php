<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MortuaryService extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prescriptions() 
    {
        return $this->hasMany(Prescription::class);
    }

    public function payments() 
    {
        return $this->hasMany(Payment::class);
    }

    public function dateCollectedBy()
    {
        return $this->belongsTo(User::class, 'date_collected_by');
    }

    public function totalPayments()
    {
        return $this->payments()->sum('amount_paid') ?? 0;
    }

    public function totalHmsBills()
    {
        return $this->prescriptions()->sum('hms_bill') ?? 0;
    }

    public function totalPaidPrescriptions()
    {
        return $this->prescriptions()->sum('paid') ?? 0;
    }
}
