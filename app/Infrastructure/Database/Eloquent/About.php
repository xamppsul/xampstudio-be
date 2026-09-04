<?php

namespace App\Infrastructure\Database\Eloquent;

use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    protected $fillable = ['description', 'experience_during', 'project_is_done', 'client_response', 'img', 'status', 'created_at'];
}
