<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function visit() 
    {
        return $this->belongsTo(Visit::class);
    }

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function patient() 
    {
        return $this->belongsTo(Patient::class);
    }

    // public function visitPayments(Visit $visit)
    // {
    //     return $visit->payments->sum('amount_paid');
    // }
}
