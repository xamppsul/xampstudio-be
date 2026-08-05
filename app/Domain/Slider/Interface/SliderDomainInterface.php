<?php

namespace App\Domain\Slider\Interface;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SliderDomainInterface
{
    public function ValidateSliderCollection(): Collection;
    public function GetSliderCollection(?string $title = null, ?string $date = null): LengthAwarePaginator;
}
