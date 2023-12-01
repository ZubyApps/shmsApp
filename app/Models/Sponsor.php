<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sponsorCategory()
    {
        return $this->belongsTo(SponsorCategory::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }
}
