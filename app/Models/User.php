<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];

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

    public function sponsorCategories()
    {
        return $this->hasMany(SponsorCategory::class);
    }

    public function sponsors()
    {
        return $this->hasMany(Sponsor::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function vitalSigns() 
    {
        return $this->hasMany(VitalSigns::class);
    }

    public function resourceCategories() 
    {
        return $this->hasMany(ResourceCategory::class);
    }

    public function resourceSubCategories() 
    {
        return $this->hasMany(ResourceSubCategory::class);
    }

    public function resources() 
    {
        return $this->hasMany(Resource::class);
    }

    public function addResources() 
    {
        return $this->hasMany(AddResourceStock::class);
    }

    public function dispenseResources() 
    {
        return $this->hasMany(DispenseResource::class);
    }

    public function resourceSuppliers() 
    {
        return $this->hasMany(ResourceSupplier::class);
    }

    public function resourceStockDates() 
    {
        return $this->hasMany(ResourceStockDate::class);
    }

    public function prescriptions() 
    {
        return $this->hasMany(Prescription::class);
    }

    public function medicationCharts() 
    {
        return $this->hasMany(MedicationChart::class);
    }

    public function payments() 
    {
        return $this->hasMany(Payment::class);
    }
}
