<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftReport extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function viewedBy()
    {
        return $this->belongsTo(User::class, 'viewed_by');
    }

    public function viewedBy1()
    {
        return $this->belongsTo(User::class, 'viewed_by_1');
    }

    public function viewedBy2()
    {
        return $this->belongsTo(User::class, 'viewed_by_2');
    }
}
