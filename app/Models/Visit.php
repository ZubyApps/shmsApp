<?php

namespace App\Models;

use App\Enum\VerificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function waitingFor()
    {
        return $this->belongsTo(User::class, 'waiting_for');
    }

    public function doctorDoneBy()
    {
        return $this->belongsTo(User::class, 'doctor_done_by');
    }

    public function nurseDoneBy()
    {
        return $this->belongsTo(User::class, 'nurse_done_by');
    }

    public function pharmacyDoneBy()
    {
        return $this->belongsTo(User::class, 'pharmacy_done_by');
    }

    public function labDoneBy()
    {
        return $this->belongsTo(User::class, 'lab_done_by');
    }

    public function billingDoneBy()
    {
        return $this->belongsTo(User::class, 'billing_done_by');
    }

    public function hmoDoneBy()
    {
        return $this->belongsTo(User::class, 'hmo_done_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function viewedBy()
    {
        return $this->belongsTo(User::class, 'viewed_by');
    }

    public function closedOpenedBy()
    {
        return $this->belongsTo(User::class, 'closed_opened_by');
    }

    public function discountBy()
    {
        return $this->belongsTo(User::class, 'discount_by');
    }

    public function statusUpdatedBy()
    {
        return $this->belongsTo(User::class, 'status_updated_by');
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

    public function payments() 
    {
        return $this->hasMany(Payment::class);
    }

    public function deliveryNotes()
    {
        return $this->hasMany(DeliveryNote::class);
    }

    public function surgeryNotes()
    {
        return $this->hasMany(SurgeryNote::class);
    }

    public function ancVitalSigns() 
    {
        return $this->hasMany(AncVitalSigns::class);
    }

    public function nursingCharts() 
    {
        return $this->hasMany(NursingChart::class);
    }

    public function medicalReports() 
    {
        return $this->hasMany(MedicalReport::class);
    }

    public function nursesReports() 
    {
        return $this->hasMany(NursesReport::class);
    }

    public function antenatalRegisteration(): HasOne
    {
        return $this->hasOne(AntenatalRegisteration::class);
    }

    public function patientsFiles() 
    {
        return $this->hasMany(PatientsFile::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function appointments() 
    {
        return $this->hasMany(Appointment::class);
    }

    public function wards(): HasOne
    {
        return $this->hasOne(Ward::class);
    }

    public function labourRecords(): HasMany
    {
        return $this->hasMany(LabourRecord::class);
    }

    public function oldestVitalSign(): HasOne
    {
        return $this->hasOne(vitalSigns::class)->oldestOfMany();
    }

    public function latestConsultation()
    {
        return $this->hasOne(Consultation::class)->latestOfMany('created_at');
    }

    public function totalHmsBills()
    {
        return $this->prescriptions()->sum('hms_bill') ?? 0;
    }

    public function totalHmsOrNhisBills()
    {
        if ($this->sponsor?->category_name === 'NHIS') {
        return $this->prescriptions()->sum('nhis_bill') ?? 0;
        }

        return $this->prescriptions()->sum('hms_bill') ?? 0;
    }

    public function totalHmoBills()
    {
        return $this->prescriptions()->sum('hmo_bill') ?? 0;
    }

    public function totalNhisBills()
    {
        return $this->prescriptions()->sum('nhis_bill') ?? 0;
    }

    public function totalApprovedBills()
    {
        return $this->prescriptions()
        ->where(function ($q) {
            $q->where('approved', true)
              ->orWhereRaw('paid >= hms_bill');
        })
        ->sum('hms_bill') ?? 0;
    }

    public function totalPayments()
    {
        return $this->payments()->sum('amount_paid') ?? 0;
    }

    public function totalPaidPrescriptions()
    {
        return $this->prescriptions()->sum('paid') ?? 0;
    }

    public function totalPrescriptionCapitations()
    {
        return $this->prescriptions()->sum('capitation') ?? 0;
    }

    public function refreshTotals()
    {
        $sums = $this->prescriptions()
            ->selectRaw('
                COALESCE(SUM(paid), 0) as totalPaid, 
                COALESCE(SUM(hms_bill), 0) as totalHmsBill, 
                COALESCE(SUM(nhis_bill), 0) as totalNhisBill
            ')
            ->first();

        return $this->update([
            'total_paid'      => $sums->totalPaid,
            'total_hms_bill'  => $sums->totalHmsBill,
            'total_nhis_bill' => $sums->totalNhisBill,
        ]);
    }
}
