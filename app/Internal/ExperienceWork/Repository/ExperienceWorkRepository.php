<?php

namespace App\Internal\ExperienceWork\Repository;

use App\Domain\ExperienceWork\Interface\ExperienceWorkDomainInterface;
use App\Infrastructure\Database\Eloquent\Core_value;
use App\Infrastructure\Database\Eloquent\Experience_work;
use App\Infrastructure\Database\Eloquent\Experience_work_achivement;
use App\Infrastructure\Database\Eloquent\Experience_work_techstack;
use App\Internal\ExperienceWork\DTO\ExperienceWorkDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

// use Illuminate\Support\Facades\DB;

class ExperienceWorkRepository implements ExperienceWorkDomainInterface
{
    public function ValidateExperienceWorkCollection(): Collection
    {
        return Experience_work::query()->get();
    }

    public function GetExperienceWorkCollection(
        ?string $title,
        ?string $start_at,
        ?string $end_at,
        ?string $position,
        ?int $limit = 10
    ): LengthAwarePaginator {
        return Experience_work::with(['achivement', 'techstack.tech'])
            #ambil pengalaman kerja paling baru
            // ->where('end_at', '<', now())
            ->getExperienceLast()
            ->when($title, function ($query) use ($title) {
                $query->where('title', 'like', "%$title%");
            })
            ->when($start_at, function ($query) use ($start_at) {
                $query->where('start_at', 'like', "%$start_at%");
            })
            ->when($end_at, function ($query) use ($end_at) {
                $query->where('end_at', 'like', "%$end_at%");
            })
            ->when($position, function ($query) use ($position) {
                $query->where('position', $position);
            })
            ->paginate($limit);
    }

    public function ValidateExperienceWorkByID(int $id): bool
    {
        return !Experience_work::whereId($id)->exists() ? false : true;
    }

    public function GetExperienceWorkByID(int $id): ?Experience_work
    {
        return Experience_work::whereId($id)
            ->with(['achivement', 'techstack.tech'])
            ->first();
    }

    // public function GetCoreValueByExperienceWorkID(int $id)
    // {
    //     return DB::table('core_values')
    //         ->where('ExperienceWorks_id', $id)
    //         ->get(["name"])
    //         ->toArray();
    // }

    public function InsertExperienceWorkData(ExperienceWorkDTO $dto): Experience_work
    {
        return Experience_work::create([
            'title' => $dto->title,
            'start_at' => $dto->start_at,
            'end_at' => $dto->end_at,
            'position' => $dto->position,
            'description' => $dto->description
        ]);
    }

    /**
     * @method InsertAchivementByExperienceWorkID()
     * @param $data <- insert core value for ExperienceWork
     */
    public function InsertAchivementByExperienceWorkID(array $data): void
    {
        Experience_work_achivement::insert($data);
    }

    /**
     * @method InsertTechStackByExperienceWorkID()
     * @param $data <- insert core value for ExperienceWork
     */
    public function InsertTechStackByExperienceWorkID(array $data): void
    {
        Experience_work_techstack::insert($data);
    }

    /**
     * @method UpdateCoreValueByExperienceWorkID()
     * @param $ExperienceWorks_id <- id ExperienceWorks #child_id dari parent_id(ExperienceWork)
     * @param $core_values_id <- id core value
     * @param $name <- store update name of corevalue ExperienceWorks
     */
    public function UpdateCoreValueByExperienceWorkID(int $ExperienceWorks_id, int $core_values_id, string $name): void
    {
        Core_value::where([
            ['id', '=', $core_values_id],
            ['ExperienceWorks_id', '=', $ExperienceWorks_id]
        ])->update(['name' => $name]);
    }

    public function UpdateExperienceWorkDataWithImg(int $id, ExperienceWorkDTO $dto): void
    {
        Experience_work::whereId($id)->update([
            'title' => $dto->title,
            'start_at' => $dto->start_at,
            'end_at' => $dto->end_at,
            'position' => $dto->position,
            'description' => $dto->description

        ]);
    }

    // public function UpdateExperienceWorkDataNoImg(int $id, ExperienceWorkDTO $dto): void
    // {
    //     Experience_work::whereId($id)->update([
    //         'description' => $dto->description,
    //         'experience_during' => $dto->experience_during,
    //         'project_is_done' => $dto->project_is_done,
    //         'client_response' => $dto->client_response,
    //         'status' => $dto->status
    //     ]);
    // }

    public function DeleteExperienceWorkData(int $id): void
    {
        Experience_work::whereId($id)->delete();
    }

    public function DeleteAchivementByExperienceWorksID(int $experience_work_id): void
    {
        Experience_work_achivement::whereexperience_works_id($experience_work_id)->delete();
    }

    public function DeleteTechByExperienceWorksID(int $experience_work_id): void
    {
        Experience_work_techstack::whereexperience_works_id($experience_work_id)->delete();
    }

    public function ValidateEndAtIsExists(ExperienceWorkDTO $dto): bool
    {
        return !empty($dto->end_at) ? true : false;
    }

    /**
     * @method RuleSetDateExperienceWork()
     * @param $dto
     * description:
     * 1. make sure isian start_at and end_at benar
     * 2. start at lebih kecil dari end at || end at harus lebih besar dari start at (tanggal terurut sesuai bulan)
     */
    public function ValidateRuleSetDateExperienceWork(ExperienceWorkDTO $dto): bool
    {
        return !($dto->start_at < $dto->end_at) || !($dto->end_at > $dto->start_at) ? false : true;
    }
}
