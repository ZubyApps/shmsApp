<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabourRecord extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'lmp' => 'date',
        'edd' => 'date',
        'onset' => 'datetime',
        'm_ruptured_at' => 'datetime',
        'contractions_began' => 'datetime',
        'spontaneous' => 'boolean',
        'induced' => 'boolean',
        'amniotomy' => 'boolean',
        'oxytocies' => 'boolean',
        'excellent' => 'boolean',
        'good' => 'boolean',
        'fair' => 'boolean',
        'poor' => 'boolean',
        'multiple' => 'boolean',
        'singleton' => 'boolean',
        'alive' => 'boolean',
        'perineum_intact' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function summarizedBy()
    {
        return $this->belongsTo(User::class, 'summarized_by');
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function partographs(): HasMany
    {
        return $this->hasMany(Partograph::class);
    }
}
