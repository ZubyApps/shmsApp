<?php

namespace App\Models;

use App\Enum\VerificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function discountBy()
    {
        return $this->belongsTo(User::class, 'discount_by');
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

    public function totalBills()
    {
        $totalBill = 0;
         foreach($this->prescriptions as $prescription){
            $totalBill += $prescription->hms_bill;
         }

         return $totalBill;
    }

    public function totalApprovedBills()
    {
        $totalApprovedBill = 0;
         foreach($this->prescriptions as $prescription){
            $totalApprovedBill += $prescription->approved ? $prescription->hms_bill : 0;
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
}
