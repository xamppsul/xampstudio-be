<?php

namespace App\Domain\About\Interface;

use App\Infrastructure\Database\Eloquent\About;
use App\Internal\About\DTO\AboutDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface AboutDomainInterface
{
    public function ValidateAboutCollection(): Collection;
    public function GetAboutCollection(?int $experience_during, ?int $project_is_done, ?int $client_response): LengthAwarePaginator;
    public function ValidateAboutByID(int $id): bool;
    public function GetAboutByID(int $id): ?About;
    // public function GetCoreValueByAboutID(int $id);
    public function InsertAboutData(AboutDTO $dto, string $pathImgAboutBase64): About;
    public function InsertCoreValuesByAboutID(array $data): void;
    public function UpdateAboutDataWithImg(int $id, AboutDTO $dto, string $pathImgAboutBase64): void;
    public function UpdateCoreValueByAboutID(int $abouts_id, int $core_values_id, string $name): void;
    public function UpdateAboutDataNoImg(int $id, AboutDTO $dto): void;
    public function DeleteAboutData(int $id): void;
    public function DeleteCoreValuesByAboutsID(int $about_id): void;
}
