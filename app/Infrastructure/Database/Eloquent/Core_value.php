<?php

namespace App\Infrastructure\Database\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Core_value extends Model
{
    protected $fillable = ['abouts_id', 'name', 'created_at'];
}
