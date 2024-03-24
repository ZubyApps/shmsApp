<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThirdParty extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function thirdPartyServies() 
    {
        return $this->hasMany(ThirdPartyService::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patientsFile()
    {
        return $this->hasOne(PatientsFile::class);
    }
}
