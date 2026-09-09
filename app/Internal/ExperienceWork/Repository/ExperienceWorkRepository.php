<?php

namespace App\Internal\ExperienceWork\Repository;

use App\Domain\ExperienceWork\Interface\ExperienceWorkDomainInterface;
use App\Infrastructure\Database\Eloquent\Core_value;
use App\Infrastructure\Database\Eloquent\Experience_work;
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
        ?int $experience_during,
        ?int $project_is_done,
        ?int $client_response
    ): LengthAwarePaginator {
        return Experience_work::with('core_values')
            ->where('status', true)
            ->when($experience_during, function ($query) use ($experience_during) {
                $query->where('experience_during', $experience_during);
            })
            ->when($project_is_done, function ($query) use ($project_is_done) {
                $query->where('project_is_done', $project_is_done);
            })
            ->when($client_response, function ($query) use ($client_response) {
                $query->where('client_response', $client_response);
            })
            ->paginate(5);
    }

    public function ValidateExperienceWorkByID(int $id): bool
    {
        return !Experience_work::whereId($id)->exists() ? false : true;
    }

    public function GetExperienceWorkByID(int $id): ?Experience_work
    {
        return Experience_work::where([
            ['id', '=', $id],
            ['status', '!=', 0]
        ])
            ->with('core_values')
            ->first();
    }

    // public function GetCoreValueByExperienceWorkID(int $id)
    // {
    //     return DB::table('core_values')
    //         ->where('ExperienceWorks_id', $id)
    //         ->get(["name"])
    //         ->toArray();
    // }

    public function InsertExperienceWorkData(ExperienceWorkDTO $dto, string $pathImgExperienceWorkBase64): Experience_work
    {
        return Experience_work::create([
            'img' => $pathImgExperienceWorkBase64,
            'description' => $dto->description,
            'experience_during' => $dto->experience_during,
            'project_is_done' => $dto->project_is_done,
            'client_response' => $dto->client_response,
            'status' => $dto->status
        ]);
    }

    /**
     * @method InsertCoreValuesByExperienceWorkID()
     * @param $data <- insert core value for ExperienceWork
     */
    public function InsertCoreValuesByExperienceWorkID(array $data): void
    {
        Core_value::insert($data);
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

    public function UpdateExperienceWorkDataWithImg(int $id, ExperienceWorkDTO $dto, string $pathImgExperienceWorkBase64): void
    {
        Experience_work::whereId($id)->update([
            'img' => $pathImgExperienceWorkBase64,
            'description' => $dto->description,
            'experience_during' => $dto->experience_during,
            'project_is_done' => $dto->project_is_done,
            'client_response' => $dto->client_response,
            'status' => $dto->status

        ]);
    }

    public function UpdateExperienceWorkDataNoImg(int $id, ExperienceWorkDTO $dto): void
    {
        Experience_work::whereId($id)->update([
            'description' => $dto->description,
            'experience_during' => $dto->experience_during,
            'project_is_done' => $dto->project_is_done,
            'client_response' => $dto->client_response,
            'status' => $dto->status
        ]);
    }

    public function DeleteExperienceWorkData(int $id): void
    {
        Experience_work::whereId($id)->delete();
    }

    public function DeleteCoreValuesByExperienceWorksID(int $ExperienceWork_id): void
    {
        Core_value::whereExperienceWorks_id($ExperienceWork_id)->delete();
    }
}
