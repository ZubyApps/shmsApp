<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sponsorCategory()
    {
        return $this->belongsTo(SponsorCategory::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function capitationPayments()
    {
        return $this->hasMany(CapitationPayment::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
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

    public function allPaidPrescriptions()
    {
        $allPayments = 0;
        foreach($this->visits as $visit){
            $allPayments += $visit->totalPaidPrescriptions();
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
