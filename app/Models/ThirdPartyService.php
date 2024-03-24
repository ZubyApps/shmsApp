<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThirdPartyService extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function thirdParty()
    {
        return $this->belongsTo(ThirdParty::class);
    }

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
