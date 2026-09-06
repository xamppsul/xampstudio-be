<?php

namespace App\Infrastructure\Database\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class About extends Model
{
    protected $fillable = [
        'description',
        'experience_during',
        'project_is_done',
        'client_response',
        'img',
        'status',
        'created_at'
    ];


    public function core_values(): HasMany
    {
        return $this->hasMany(Core_value::class, 'abouts_id');
    }
}
