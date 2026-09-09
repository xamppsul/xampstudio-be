<?php

namespace App\Infrastructure\Database\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Experience_work_tech_stack extends Model
{
    protected $fillable = [
        'experience_works_id',
        'techstacks_id',
        'created_at'
    ];

    public function tech(): BelongsTo
    {
        return $this->belongsTo(Techstack::class, 'techstacks_id');
    }

    public function experience_work(): BelongsTo
    {
        return $this->belongsTo(Experience_work::class, 'experience_works_id');
    }
}
