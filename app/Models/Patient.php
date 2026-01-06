<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function flaggedBy()
    {
        return $this->belongsTo(User::class, 'flagged_by');
    }

    public function sponsor()
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function antenatalRegisteration(): HasOne
    {
        return $this->hasOne(AntenatalRegisteration::class);
    }

    public function medicalReports() 
    {
        return $this->hasMany(MedicalReport::class);
    }

    public function nursesReport() 
    {
        return $this->hasMany(NursesReport::class);
    }

    public function appointments() 
    {
        return $this->hasMany(Appointment::class);
    }

    public function latestVisit(): HasOne
    {
        return $this->hasOne(Visit::class)->latestOfMany();
    }

    public function patientId()
    {
        return $this->card_no.' '.$this->first_name.' '.$this->middle_name.' '.$this->last_name;
    }

    public function fullName()
    {
        return $this->first_name.' '.$this->middle_name.' '.$this->last_name;
    }

    public function age()
    {
        return str_replace(['a', 'g', 'o'], '', (new Carbon($this->date_of_birth))->diffForHumans(['other' => null, 'parts' => 2, 'short' => true]), );
    }

    public function patientFullInfo()
    {
        return $this->fullName().', '.$this->age().', '.$this->sex;
    }

    public function canSms()
    {
        return $this->sms && ($this->phone !== '00000000000');
    }

    public function allHmsBills()
    {
        return Prescription::whereHas('visit', fn($q) => $q->where('patient_id', $this->id))->sum('hms_bill') ?? 0;
    }

    public function allHmsOrNhisBills()
    {
        return Prescription::join('visits', 'prescriptions.visit_id', '=', 'visits.id')
        ->join('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
        ->where('visits.patient_id', $this->id)
        ->sum(DB::raw("CASE WHEN sponsors.category_name = 'NHIS' THEN prescriptions.nhis_bill ELSE prescriptions.hms_bill END"))
        ?? 0;
    }

    public function allHmoBills()
    {
        return Prescription::whereHas('visit', fn($q) => $q->where('patient_id', $this->id))->sum('hmo_bill') ?? 0;
    }

    public function allNhisBills()
    {
        return Prescription::whereHas('visit', fn($q) => $q->where('patient_id', $this->id))->sum('nhis_bill') ?? 0;
    }

    public function allPayments()
    {
        return Payment::whereHas('visit', fn($q) => $q->where('patient_id', $this->id))->sum('amount_paid') ?? 0;
    }

    public function allPaidPrescriptions()
    {
        return Prescription::whereHas('visit', fn($q) => $q->where('patient_id', $this->id))->sum('paid') ?? 0;
    }

    public function allPaid()
    {
        return Visit::where('patient_id', $this->id)->sum('total_paid') ?? 0;
    }

    public function allDiscounts()
    {
        return Visit::where('patient_id', $this->id)->sum('discount') ?? 0;
    }
}
