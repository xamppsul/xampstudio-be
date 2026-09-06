<?php

namespace App\Infrastructure\Database\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Core_value extends Model
{
    protected $fillable = ['abouts_id', 'name', 'created_at'];

    public function About(): BelongsTo
    {
        return $this->belongsTo(About::class, 'abouts_id');
    }
}
