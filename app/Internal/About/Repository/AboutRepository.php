<?php

namespace App\Internal\About\Repository;

use App\Domain\About\Interface\AboutDomainInterface;
use App\Infrastructure\Database\Eloquent\About;
use App\Internal\About\DTO\AboutDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AboutRepository implements AboutDomainInterface
{
    public function ValidateAboutCollection(): Collection
    {
        return About::query()->get();
    }

    public function GetAboutCollection(?string $title = null, ?string $date = null): LengthAwarePaginator
    {
        return About::where('status', true)
            ->when($title, function ($query) use ($title) {
                $query->where('title', 'like', "%{$title}%");
            })->when($date, function ($query) use ($date) {
                $query->where('created_at', $date);
            })
            ->orderBy('position', 'asc')
            ->paginate(10);
    }

    public function ValidateAboutByID(int $id): bool
    {
        return !About::whereId($id)->exists() ? false : true;
    }

    public function GetAboutByID(int $id): About
    {
        return About::whereId($id)->first();
    }

    public function InsertAboutData(AboutDTO $dto, string $pathImgAboutBase64): void
    {
        About::create([
            'img' => $pathImgAboutBase64,
            'title' => $dto->title,
            'description' => $dto->description,
            'position' => $dto->position,
            'status' => $dto->status,
        ]);
    }

    public function UpdateAboutDataWithImg(int $id, AboutDTO $dto, string $pathImgAboutBase64): void
    {
        About::whereId($id)->update([
            'img' => $pathImgAboutBase64,
            'title' => $dto->title,
            'description' => $dto->description,
            'position' => $dto->position,
            'status' => $dto->status,
        ]);
    }

    public function UpdateAboutDataNoImg(int $id, AboutDTO $dto): void
    {
        About::whereId($id)->update([
            'title' => $dto->title,
            'description' => $dto->description,
            'position' => $dto->position,
            'status' => $dto->status,
        ]);
    }

    public function DeleteAboutData(int $id): void
    {
        About::whereId($id)->delete();
    }
}
