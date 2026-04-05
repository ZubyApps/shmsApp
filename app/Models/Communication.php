<?php

namespace App\Models;

use App\Enum\CommunicationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Communication extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    const UPDATED_AT = null;

    protected $casts = [
        'type_id' => CommunicationType::class,
    ];
}
