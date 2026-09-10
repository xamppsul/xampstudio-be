<?php

namespace App\Infrastructure\Database\Eloquent;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Experience_work extends Model
{
    protected $appends = ['achivement_list', 'tech_stack_list'];
    protected $hidden = ['achivement', 'techstack'];
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

    public function scopeGetExperienceLast($query)
    {
        return $query
            ->orderByRaw('CASE WHEN end_at IS NULL THEN 0 ELSE 1 END ASC')
            ->orderBy('end_at', 'desc')
            ->orderBy('start_at', 'desc');
    }

    /**
     * eloquent accessor(achivement list) buat ubah format data
     * (masuk ke key achive untuk mengubah dari list object menjadi list array)
     **/
    protected function achivementList(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->relationLoaded('achivement')
                ? $this->achivement->pluck('achive')
                : []
        );
    }

    /**
     * eloquent accessor(techstack list) buat ubah format data
     * (masuk ke key achive untuk mengubah dari list object menjadi list array)
     **/
    protected function techStackList(): Attribute
    {
        return Attribute::make(
            get: function () {
                $techstack_load = $this->relationLoaded('techstack');
                if ($techstack_load) {
                    return $this->techstack->map(fn($item) => $item->tech->tech);
                }
            }
        );
    }
}
