<?php

namespace App\Domain\ExperienceWork\Interface;

use App\Infrastructure\Database\Eloquent\Experience_work;
use App\Internal\ExperienceWork\DTO\ExperienceWorkDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ExperienceWorkDomainInterface
{
    public function ValidateExperienceWorkCollection(): Collection;
    public function GetExperienceWorkCollection(?int $experience_during, ?int $project_is_done, ?int $client_response): LengthAwarePaginator;
    public function ValidateExperienceWorkByID(int $id): bool;
    public function GetExperienceWorkByID(int $id): ?Experience_work;
    // public function GetCoreValueByExperienceWorkID(int $id);
    public function InsertExperienceWorkData(ExperienceWorkDTO $dto, string $pathImgExperienceWorkBase64): Experience_work;
    public function InsertCoreValuesByExperienceWorkID(array $data): void;
    public function UpdateExperienceWorkDataWithImg(int $id, ExperienceWorkDTO $dto, string $pathImgExperienceWorkBase64): void;
    public function UpdateCoreValueByExperienceWorkID(int $experience_works_id, int $core_values_id, string $name): void;
    public function UpdateExperienceWorkDataNoImg(int $id, ExperienceWorkDTO $dto): void;
    public function DeleteExperienceWorkData(int $id): void;
    public function DeleteCoreValuesByExperienceWorksID(int $experience_work_id): void;
}
