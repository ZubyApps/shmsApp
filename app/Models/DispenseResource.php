<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispenseResource extends Model
{
    use HasFactory;

    protected $guarded = [];

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
}
