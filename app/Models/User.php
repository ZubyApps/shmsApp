<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function sponsorCategories(): HasMany
    {
        return $this->hasMany(SponsorCategory::class);
    }

    public function sponsors(): HasMany
    {
        return $this->hasMany(Sponsor::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function vitalSigns(): HasMany 
    {
        return $this->hasMany(VitalSigns::class);
    }

    public function resourceCategories(): HasMany 
    {
        return $this->hasMany(ResourceCategory::class);
    }

    public function resourceSubCategories(): HasMany 
    {
        return $this->hasMany(ResourceSubCategory::class);
    }

    public function resources(): HasMany 
    {
        return $this->hasMany(Resource::class);
    }

    public function addResources(): HasMany 
    {
        return $this->hasMany(AddResourceStock::class);
    }

    public function dispenseResources(): HasMany 
    {
        return $this->hasMany(DispenseResource::class);
    }

    public function resourceSuppliers(): HasMany 
    {
        return $this->hasMany(ResourceSupplier::class);
    }

    public function resourceStockDates(): HasMany 
    {
        return $this->hasMany(ResourceStockDate::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function medicationCharts(): HasMany
    {
        return $this->hasMany(MedicationChart::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function deliveryNotes(): HasMany
    {
        return $this->hasMany(DeliveryNote::class);
    }

    public function surgeryNotes(): HasMany
    {
        return $this->hasMany(SurgeryNote::class);
    }

    public function antenatalRegisterations(): HasMany
    {
        return $this->hasMany(AntenatalRegisteration::class);
    }

    public function ancVitalSigns(): HasMany
    {
        return $this->hasMany(AncVitalSigns::class);
    }

    public function designation(): HasOne
    {
        return $this->hasOne(Designation::class);
    }

    public function nameInFull()
    {
        return $this->firstname.' '.$this->middlename.' '.$this->lastname;
    }
}
