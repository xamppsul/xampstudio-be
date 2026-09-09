<?php

namespace App\Infrastructure\Database\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Techstack extends Model
{
    protected $fillable = ['stack', 'tech', 'created_at'];

    public function experience_work_tech_stack(): HasMany
    {
        return $this->hasMany(Experience_work_tech_stack::class, 'techstacks_id');
    }
}
