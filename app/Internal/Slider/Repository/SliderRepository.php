<?php

namespace App\Internal\Slider\Repository;

use App\Domain\Slider\Interface\SliderDomainInterface;
use App\Infrastructure\Database\Eloquent\Slider;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SliderRepository implements SliderDomainInterface
{
    public function ValidateSliderCollection(): Collection
    {
        return Slider::query()->get();
    }

    public function GetSliderCollection(?string $title = null, ?string $date = null): LengthAwarePaginator
    {
        return Slider::when($title, function ($query) use ($title) {
            $query->where('title', 'like', "%{$title}%");
        })->when($date, function ($query) use ($date) {
            $query->where('created_at', $date);
        })
            ->orderBy('position', 'asc')
            ->paginate(10);
    }

    public function ValidateSliderByID(int $id): bool
    {
        return !Slider::whereId($id)->exists() ? false : true;
    }

    public function GetSliderByID(int $id): Slider
    {
        return Slider::whereId($id)->first();
    }
}
