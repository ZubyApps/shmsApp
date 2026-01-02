<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftPerformance extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'shift_start' => 'datetime',
        'shift_end'   => 'datetime',
        'staff'       => 'array',
        'performance' => 'float',
    ];
}
