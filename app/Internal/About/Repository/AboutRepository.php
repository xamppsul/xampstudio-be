<?php

namespace App\Internal\About\Repository;

use App\Domain\About\Interface\AboutDomainInterface;
use App\Infrastructure\Database\Eloquent\About;
use App\Infrastructure\Database\Eloquent\Core_value;
use App\Internal\About\DTO\AboutDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AboutRepository implements AboutDomainInterface
{
    public function ValidateAboutCollection(): Collection
    {
        return About::query()->get();
    }

    public function GetAboutCollection(?int $experience_during, ?int $project_is_done, ?int $client_response): LengthAwarePaginator
    {
        return About::where('status', true)
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

    public function ValidateAboutByID(int $id): bool
    {
        return !About::whereId($id)->exists() ? false : true;
    }

    public function GetAboutByID(int $id): About
    {
        return About::whereId($id)->first();
    }

    public function GetCoreValueByAboutID(int $id)
    {
        return DB::table('core_values')
            ->where('abouts_id', $id)
            ->get(["name"])
            ->toArray();
    }

    public function InsertAboutData(AboutDTO $dto, string $pathImgAboutBase64): About
    {
        return About::create([
            'img' => $pathImgAboutBase64,
            'description' => $dto->description,
            'experience_during' => $dto->experience_during,
            'project_is_done' => $dto->project_is_done,
            'client_response' => $dto->client_response,
            'status' => $dto->status
        ]);
    }

    /**
     * @method InsertCoreValuesByAboutID()
     * @param $data <- insert core value for about
     */
    public function InsertCoreValuesByAboutID(array $data): void
    {
        Core_value::insert($data);
    }

    public function UpdateAboutDataWithImg(int $id, AboutDTO $dto, string $pathImgAboutBase64): void
    {
        About::whereId($id)->update([
            'img' => $pathImgAboutBase64,
            'description' => $dto->description,
            'experience_during' => $dto->experience_during,
            'project_is_done' => $dto->project_is_done,
            'client_response' => $dto->client_response,
            'status' => $dto->status

        ]);
    }

    public function UpdateAboutDataNoImg(int $id, AboutDTO $dto): void
    {
        About::whereId($id)->update([
            'description' => $dto->description,
            'experience_during' => $dto->experience_during,
            'project_is_done' => $dto->project_is_done,
            'client_response' => $dto->client_response,
            'status' => $dto->status
        ]);
    }

    public function DeleteAboutData(int $id): void
    {
        About::whereId($id)->delete();
    }
}
