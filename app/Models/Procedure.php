<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procedure extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dateBookedBy()
    {
        return $this->belongsTo(User::class, 'date_booked_by');
    }

    public function statusUpdatedBy()
    {
        return $this->belongsTo(User::class, 'status_updated_by');
    }
}
