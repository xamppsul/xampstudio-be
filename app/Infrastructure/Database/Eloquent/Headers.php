<?php

namespace App\Infrastructure\Database\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Headers extends Model
{
    protected $fillable = ['key', 'platform', 'version'];
}
