<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function visit() 
    {
        return $this->belongsTo(Visit::class);
    }
    
    public function sponsor()
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function firstReminderBy()
    {
        return $this->belongsTo(User::class, 'first_reminder_by');
    }

    public function secondReminderBy()
    {
        return $this->belongsTo(User::class, 'second_reminder_by');
    }

    public function finalReminderBy()
    {
        return $this->belongsTo(User::class, 'final_reminder_by');
    }

    public function confirmedPaidBy()
    {
        return $this->belongsTo(User::class, 'confirmed_paid_by');
    }
}
