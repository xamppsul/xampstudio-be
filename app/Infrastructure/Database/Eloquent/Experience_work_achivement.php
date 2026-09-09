<?php

namespace App\Infrastructure\Database\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Experience_work_achivement extends Model
{
    protected $fillable = [
        'experience_works_id',
        'achive',
        'created_at'
    ];

    public function experience_work(): BelongsTo
    {
        return $this->belongsTo(Experience_work::class, 'experience_works_id');
    }
}
