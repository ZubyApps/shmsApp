<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

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

    public function patientPreForm(): HasMany
    {
        return $this->hasMany(PatientPreForm::class);
    }

    public function allHmsBills()
    {
        return Visit::where('sponsor_id', $this->id)->sum('total_hms_bill') ?? 0;
    }

    public function allHmoBills()
    {
        return Prescription::whereHas('visit', fn($q) => $q->where('sponsor_id', $this->id))->sum('hmo_bill') ?? 0;
    }

    public function allNhisBills()
    {
        return Visit::where('sponsor_id', $this->id)->sum('total_nhis_bill') ?? 0;
    }

    public function allPayments()
    {
        return Visit::where('sponsor_id', $this->id)->sum('total_paid') ?? 0;
    }

    public function allPaidPrescriptions()
    {
        return Prescription::whereHas('visit', fn($q) => $q->where('sponsor_id', $this->id))->sum('paid') ?? 0;
    }

    public function allPaid()
    {
        return Visit::where('sponsor_id', $this->id)->sum('total_paid') ?? 0;
    }

    public function allDiscounts()
    {
        return Visit::where('sponsor_id', $this->id)->sum('discount') ?? 0;
    }

    public function resources()
    {
        return $this->belongsToMany(Resource::class)
                    ->using(ResourceSponsor::class)
                    ->withPivot('selling_price', 'user_id')
                    ->withTimestamps();
    }

    public function resourceSponsors()
    {
        return $this->hasMany(ResourceSponsor::class, 'sponsor_id');
    }

    public function scopeHmoDeptCategories(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('category_name', 'HMO')
              ->orWhere('category_name', 'NHIS')
              ->orWhere('category_name', 'Retainership');
        });
    }
}
