<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ResourceCategorySponsor extends Pivot
{
    use HasFactory;

    protected $table = 'resource_category_sponsor';

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sponsor()
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function resourceCategory()
    {
        return $this->belongsTo(ResourceCategory::class);
    }
}
