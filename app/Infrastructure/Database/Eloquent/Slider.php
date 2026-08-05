<?php

namespace App\Infrastructure\Database\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = ['img', 'title', 'description', 'position'];
}
