<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function discontinuedBy()
    {
        return $this->belongsTo(User::class, 'discontinued_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function doctorOnCall()
    {
        return $this->belongsTo(User::class, 'doctor_on_call');
    }

    public function heldBy()
    {
        return $this->belongsTo(User::class, 'held_by');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function medicationCharts() 
    {
        return $this->hasMany(MedicationChart::class);
    }

    public function nursingCharts() 
    {
        return $this->hasMany(NursingChart::class);
    }

    public function thirdPartyServices() 
    {
        return $this->hasMany(ThirdPartyService::class);
    }

    public function procedure() 
    {
        return $this->hasOne(Procedure::class);
    }

    public function forPharmacy(int $conId)
    {
        return $this->where('consultation_id', $conId)
                    ->where(function(Builder $query) {
                        $query->whereRelation('resource', 'category', 'Medications')
                              ->orWhereRelation('resource', 'category', 'Consumables');
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    public function prescriptionsCharted($visiId, $chartTable, $comparism = '=')
    {
        return $this->where('visit_id', $visiId)
                    ->where('chartable', true)
                    ->where('discontinued', false)
                    ->whereDoesntHave($chartTable)
                    ->whereRelation('resource', 'sub_category', $comparism ,'Injectable')
                    ->count();
    }

    public function otherPrescriptions($visiId)
    {
        return $this->where('visit_id', $visiId)
                    ->where('chartable', false)
                    ->where(function(Builder $query) {
                        $query->whereRelation('resource', 'category', 'Medications')
                              ->orWhereRelation('resource', 'category', 'Consumables');
                    })
                    ->count();
    }

    public function prescriptionsChartedPerShift($shift, $chartTable, $comparism = '=')
    {
        $shiftEnd = new Carbon($shift->shift_end);
        $shiftEndTimer = $shiftEnd->subMinutes(20);

        return $this->where('chartable', true)
                    ->where('held', null)
                    ->where('discontinued', false)
                    ->whereRelation('resource', 'sub_category', $comparism ,'Injectable')
                    ->where(function(Builder $query) use($chartTable) {
                        $query->whereHas($chartTable);
                        })
                    // ->whereBetween('created_at', [$shift->shift_start, $shiftEndTimer])
                    ->whereBetween('hms_bill_date', [$shift->shift_start, $shiftEndTimer])
                    ->count();
    }

    public function prescriptionsNotChartedPerShift($shift, $chartTable, $comparism = '=')
    {
        $shiftEnd = new Carbon($shift->shift_end);
        $shiftEndTimer = $shiftEnd->subMinutes(20);
        return $this->where('chartable', true)
                    ->where('held', null)
                    ->where('discontinued', false)
                    ->whereRelation('resource', 'sub_category', $comparism ,'Injectable')
                    ->where(function(Builder $query) use($chartTable) {
                        $query->whereDoesntHave($chartTable);
                        })
                    // ->whereBetween('created_at', [$shift->shift_start, $shiftEndTimer])
                    ->whereBetween('hms_bill_date', [$shift->shift_start, $shiftEndTimer])
                    ->get();
    }

    // public function prescriptionsGivenPerShift($shift, $chartTable, $comparism = '=')
    // {
    //     $shiftEnd       = new Carbon($shift->shift_end);
    //     $shiftEndTimer  = $shiftEnd->subMinutes(20);
    //     $column         = $comparism == '=' ? 'time_given' : 'time_done';
    //     return $this->where('chartable', true)
    //                 ->where('held', null)
    //                 ->where('discontinued', false)
    //                 // ->whereRelation('resource', 'category', $comparism ,'Medications')
    //                 ->whereRelation('resource', 'sub_category', $comparism ,'Injectable')
    //                 ->where(function(Builder $query) use($chartTable, $shift, $shiftEndTimer, $column) {
    //                     $query->whereHas($chartTable, function(Builder $query) use($shift, $shiftEndTimer, $column) {
    //                         $query->where($column, '!=', null)
    //                         ->whereBetween('scheduled_time', [$shift->shift_start, $shiftEndTimer]);
    //                     });               
    //                     //     ->orWhereHas('nursingCharts', function(Builder $query) use($shift, $shiftEndTimer) {
    //                     //     $query->where('time_done', '!=', null)
    //                     //     ->whereBetween('scheduled_time', [$shift->shift_start, $shiftEndTimer]);
    //                     // });
    //                 })              
    //                 // ->whereBetween('created_at', [$shift->shift_start, $shiftEndTimer])
    //                 ->whereBetween('hms_bill_date', [$shift->shift_start, $shiftEndTimer])
    //                 ->count();
    // }
    public function prescriptionsGivenPerShift($shift, $chartTable, $comparism = '=')
    {
        $shiftEnd       = new Carbon($shift->shift_end);
        $shiftEndTimer  = $shiftEnd->subMinutes(20);
        $column         = $comparism == '=' ? 'time_given' : 'time_done';
        return $this->where('chartable', true)
                    ->where('held', null)
                    ->where('discontinued', false)
                    ->whereRelation('resource', 'sub_category', $comparism ,'Injectable')
                    ->whereHas($chartTable, function(Builder $query) use($shift, $shiftEndTimer, $column) {
                            $query->whereNot($column);
                            // ->whereBetween('scheduled_time', [$shift->shift_start, $shiftEndTimer]);
                        })            
                    // ->whereBetween('created_at', [$shift->shift_start, $shiftEndTimer])
                    ->whereBetween('hms_bill_date', [$shift->shift_start, $shiftEndTimer])
                    ->count();
    }

    public function prescriptionsNotGivenPerShift($shift, $chartTable, $operator = '=')
    {
        $shiftEnd = new Carbon($shift->shift_end);
        $shiftEndTimer = $shiftEnd->subMinutes(20);
        $column         = $operator == '=' ? 'time_given' : 'time_done';

        return $this->where('chartable', true)
                    ->where('held', null)
                    ->where('discontinued', false)
                    // ->whereRelation('resource', 'category', $comparism ,'Medications')
                    ->whereRelation('resource', 'sub_category', $operator ,'Injectable')
                    ->where(function(Builder $query) use($chartTable, $shift, $shiftEndTimer, $column) {
                        $query->whereHas($chartTable, function(Builder $query) use($shift, $shiftEndTimer, $column) {
                            $query->where($column, null)
                            ->whereBetween('scheduled_time', [$shift->shift_start, $shiftEndTimer]);
                        });                
                        //     ->orWhereHas('nursingCharts', function(Builder $query) use($shift, $shiftEndTimer) {
                        //     $query->whereNull('time_done')
                        //     ->whereBetween('scheduled_time', [$shift->shift_start, $shiftEndTimer]);
                        // });
                    })              
                    // ->whereBetween('created_at', [$shift->shift_start, $shiftEndTimer])
                    ->whereBetween('hms_bill_date', [$shift->shift_start, $shiftEndTimer])
                    ->get();
    }
}
