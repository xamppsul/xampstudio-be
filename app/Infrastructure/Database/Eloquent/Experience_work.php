<?php

namespace App\Infrastructure\Database\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Experience_work extends Model
{
    protected $fillable = [
        'title',
        'start_at',
        'end_at',
        'position',
        'description',
        'created_at'
    ];


    public function achivement(): HasMany
    {
        return $this->hasMany(Experience_work_achivement::class, 'experience_works_id');
    }

    public function techstack(): HasMany
    {
        return $this->hasMany(Experience_work_techstack::class, 'experience_works_id');
    }
}
