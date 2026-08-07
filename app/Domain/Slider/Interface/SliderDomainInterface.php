<?php

namespace App\Domain\Slider\Interface;

use App\Infrastructure\Database\Eloquent\Slider;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SliderDomainInterface
{
    public function ValidateSliderCollection(): Collection;
    public function GetSliderCollection(?string $title = null, ?string $date = null): LengthAwarePaginator;
    public function ValidateSliderByID(int $id): bool;
    public function GetSliderByID(int $id): Slider;
}
