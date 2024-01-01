<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resultBy()
    {
        return $this->belongsTo(User::class, 'result_by');
    }

    public function hmsBillBy()
    {
        return $this->belongsTo(User::class, 'hms_bill_by');
    }

    public function hmoBillBy()
    {
        return $this->belongsTo(User::class, 'hmo_bill_by');
    }

    public function dispensedBy()
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function medicationCharts() 
    {
        return $this->hasMany(MedicationChart::class);
    }

    public function forPharmacy(int $conId)
    {
        return $this->where('consultation_id', $conId)
                    ->where(function(Builder $query) {
                        $query->whereRelation('resource', 'category', 'Medications')
                              ->orWhereRelation('resource', 'category', 'Consumables');
                    })
                    // ->where('qty_dispensed', null)
                    // ->orderBy('created_at', 'desc')
                    ->get();
    }
}
