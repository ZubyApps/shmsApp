<?php

namespace App\Models;

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

    public function bulkRequests(): HasMany
    {
        return $this->HasMany(BulkRequest::class);
    }

    public function nursingCharts(): HasMany
    {
        return $this->hasMany(NursingChart::class);
    }

    public function payMethods(): HasMany
    {
        return $this->hasMany(PayMethod::class);
    }

    public function medicalReports() 
    {
        return $this->hasMany(MedicalReport::class);
    }

    public function nursesReports() 
    {
        return $this->hasMany(NursesReport::class);
    }

    public function shiftReports() 
    {
        return $this->hasMany(ShiftReport::class);
    }

    public function capitationPayments() 
    {
        return $this->hasMany(CapitationPayment::class);
    }

    public function expenseCategories() 
    {
        return $this->hasMany(ExpenseCategory::class);
    }

    public function expenses() 
    {
        return $this->hasMany(Expense::class);
    }

    public function thirdParties() 
    {
        return $this->hasMany(ThirdParty::class);
    }

    public function thirdPartyServies() 
    {
        return $this->hasMany(ThirdPartyService::class);
    }

    public function patientsFiles() 
    {
        return $this->hasMany(PatientsFile::class);
    }

    public function medicationCategories(): HasMany 
    {
        return $this->hasMany(MedicationCategory::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function nameInFull()
    {
        return $this->firstname.' '.$this->middlename.' '.$this->lastname;
    }

    public function patientsPreForm(): HasMany
    {
        return $this->hasMany(PatientPreForm::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function wards(): HasMany
    {
        return $this->hasMany(Ward::class);
    }

    public function markedFors(): HasMany
    {
        return $this->hasMany(MarkedFor::class);
    }

    public function unitDescriptions(): HasMany
    {
        return $this->hasMany(UnitDescription::class);
    }
}
