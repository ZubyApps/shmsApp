<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationChart extends Model
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

    public function giver()
    {
        return $this->belongsTo(User::class, 'giver_id');
    }
}
