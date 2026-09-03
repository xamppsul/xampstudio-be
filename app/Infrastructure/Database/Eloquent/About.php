<?php

namespace App\Infrastructure\Database\Eloquent;

use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    protected $fillable = ['description', 'core_values_id', 'experience_during', 'project_is_done', 'client_response', 'img', 'created_at'];
}
