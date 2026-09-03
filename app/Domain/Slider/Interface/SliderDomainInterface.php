<?php

namespace App\Domain\Slider\Interface;

use App\Infrastructure\Database\Eloquent\Slider;
use App\Internal\Slider\DTO\SliderDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SliderDomainInterface
{
    public function ValidateSliderCollection(): Collection;
    public function GetSliderCollection(?string $title = null, ?string $date = null): LengthAwarePaginator;
    public function ValidateSliderByID(int $id): bool;
    public function GetSliderByID(int $id): Slider;
    public function InsertSliderData(SliderDTO $dto, string $pathImgSliderBase64): void;
    public function UpdateSliderDataWithImg(int $id, SliderDTO $dto, string $pathImgSliderBase64): void;
    public function UpdateSliderDataNoImg(int $id, SliderDTO $dto): void;
    public function DeleteSliderData(int $id): void;
}
