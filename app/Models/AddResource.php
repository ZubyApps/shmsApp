<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddResource extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function resourceSupplier()
    {
        return $this->belongsTo(ResourceSupplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
