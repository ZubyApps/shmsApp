<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class Patient extends Model
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function allHmsBills()
    {
        $allHmsBills = 0;
        foreach($this->visits as $visit){
            $allHmsBills += $visit->totalHmsBills();
        }

        return $allHmsBills;
    }

    public function allHmoBills()
    {
        $allHmoBills = 0;
        foreach($this->visits as $visit){
            $allHmoBills += $visit->totalHmoBills();
        }

        return $allHmoBills;
    }

    public function allNhisBills()
    {
        $allNhisBills = 0;
        foreach($this->visits as $visit){
            $allNhisBills += $visit->totalNhisBills();
        }

        return $allNhisBills;
    }

    public function allPayments()
    {
        $allPayments = 0;
        foreach($this->visits as $visit){
            $allPayments += $visit->totalPayments();
        }

        return $allPayments;
    }

    public function allPaid()
    {
        $allPaid = 0;
        foreach($this->visits as $visit){
            $allPaid += $visit->total_paid;
        }

        return $allPaid;
    }

    public function allDiscounts()
    {
        $allDiscounts = 0;
        foreach($this->visits as $visit){
            $allDiscounts += $visit->discount;
        }

        return $allDiscounts;
    }
}
