<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AncVitalSigns extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function antenatalRegisteration(): BelongsTo
    {
        return $this->belongsTo(AntenatalRegisteration::class);
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }    
}
