<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestigationsList extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function walkIn()
    {
        return $this->belongsTo(WalkIn::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the states
    public const PENDING = 0;
    public const SAMPLE_COLLECTED   = 1;
    public const RESULT_READY  = 2;

    // Optional: A helper to get human-readable labels
    public static function getStatuses(): array 
    {
        return [
            self::PENDING => 'Pending',
            self::SAMPLE_COLLECTED   => 'Sample Collected',
            self::RESULT_READY  => 'Result Ready',
        ];
    }


}
