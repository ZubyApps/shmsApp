<?php

namespace App\Models;

use App\Enum\VerificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

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

    public function ward(): HasOne
    {
        return $this->hasOne(Ward::class);
    }

    public function totalHmsBills()
    {
        $totalBill = 0;
         foreach($this->prescriptions as $prescription){
            $totalBill += $prescription->hms_bill;
         }

         return $totalBill;
    }

    public function totalHmsOrNhisBills()
    {
        $totalBill = 0;
         foreach($this->prescriptions as $prescription){
            $totalBill += ($this->sponsor->category_name == 'NHIS' ?  $prescription->hms_bill/10 : $prescription->hms_bill);
         }

         return $totalBill;
    }

    public function totalHmoBills()
    {
        $totalHmoBill = 0;
         foreach($this->prescriptions as $prescription){
            $totalHmoBill += $prescription->hmo_bill;
         }

         return $totalHmoBill;
    }

    public function totalNhisBills()
    {
        $totalNhisBill = 0;
         foreach($this->prescriptions as $prescription){
            $totalNhisBill += $prescription->approved ? $prescription->hms_bill/10 : $prescription->hms_bill;
         }

         return $totalNhisBill;
    }

    public function totalApprovedBills()
    {
        $totalApprovedBill = 0;
         foreach($this->prescriptions as $prescription){
            $totalApprovedBill += $prescription->approved || $prescription->paid >= $prescription->hms_bill ? $prescription->hms_bill : 0;
         }

         return $totalApprovedBill;
    }

    public function totalPayments()
    {
        $totalPayment = 0;
        foreach($this->payments as $payment){
            $totalPayment += $payment->amount_paid;
        }
        
        return $totalPayment;
    }

    public function totalPaidPrescriptions()
    {
        $totalPayments = 0;
        foreach($this->prescriptions as $prescription){
            $totalPayments += $prescription->paid;
        }
        
        return $totalPayments;
    }

    public function totalPrescriptionCapitations()
    {
        $totalPayments = 0;
        foreach($this->prescriptions as $prescription){
            $totalPayments += $prescription->capitation;
        }
        
        return $totalPayments;
    }

    public function oldestVitalSign(): HasOne
    {
        return $this->hasOne(vitalSigns::class)->oldestOfMany();
    }
}
