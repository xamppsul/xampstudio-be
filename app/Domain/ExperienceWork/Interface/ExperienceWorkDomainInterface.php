<?php

namespace App\Domain\ExperienceWork\Interface;

use App\Infrastructure\Database\Eloquent\Experience_work;
use App\Internal\ExperienceWork\DTO\ExperienceWorkDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ExperienceWorkDomainInterface
{
    public function ValidateExperienceWorkCollection(): Collection;
    public function GetExperienceWorkCollection(
        ?string $title,
        ?string $start_at,
        ?string $end_at,
        ?string $position,
        ?int $limit = 10
    ): LengthAwarePaginator;
    public function ValidateExperienceWorkByID(int $id): bool;
    public function GetExperienceWorkByID(int $id): ?Experience_work;
    // public function GetCoreValueByExperienceWorkID(int $id);
    public function InsertExperienceWorkData(ExperienceWorkDTO $dto): Experience_work;
    public function InsertAchivementByExperienceWorkID(array $data): void;
    public function InsertTechStackByExperienceWorkID(array $data): void;
    public function UpdateExperienceWorkDataWithImg(int $id, ExperienceWorkDTO $dto): void;
    public function UpdateCoreValueByExperienceWorkID(int $experience_works_id, int $core_values_id, string $name): void;
    // public function UpdateExperienceWorkDataNoImg(int $id, ExperienceWorkDTO $dto): void;
    public function DeleteExperienceWorkData(int $id): void;
    public function DeleteAchivementByExperienceWorksID(int $experience_work_id): void;
    public function DeleteTechByExperienceWorksID(int $experience_work_id): void;

    public function ValidateEndAtIsExists(ExperienceWorkDTO $dto): bool;
    public function ValidateRuleSetDateExperienceWork(ExperienceWorkDTO $dto): bool;
}
