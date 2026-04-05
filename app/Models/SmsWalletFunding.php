<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsWalletFunding extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_PAID    = 'paid';
    const STATUS_FAILED  = 'failed';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending Verification',
            self::STATUS_PAID    => 'Payment Confirmed',
            self::STATUS_FAILED  => 'Payment Declined',
            default              => 'Unknown Status',
        };
    }
}
