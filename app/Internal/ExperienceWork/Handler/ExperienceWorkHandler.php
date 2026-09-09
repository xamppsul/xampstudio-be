<?php

namespace App\Internal\ExperienceWork\Handler;

use App\Domain\ExperienceWork\Entities\ExperienceWorkDomainEntities;
use App\Infrastructure\Request\ExperienceWorkRequestInfrastructure;
use App\Internal\ExperienceWork\Const\ExperienceWorkConst;
use App\Internal\ExperienceWork\DTO\ExperienceWorkDTO;
use App\Internal\ExperienceWork\Usecase\ExperienceWorkUsecase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExperienceWorkHandler extends ExperienceWorkConst
{
    private $usecase;
    public function __construct(ExperienceWorkUsecase $usecase)
    {
        $this->usecase = $usecase;
    }

    public function index(Request $request): JsonResponse|Collection|LengthAwarePaginator
    {
        try {
            return $this->usecase->index($request);
        } catch (\Exception $error) {
            Log::error("Internal error index api: {$error->getMessage()}");
            return config('app.debug') != true ? $this->Response(500, "Maaf ada kesalahan pada sistem") : $this->Response(500, $error->getMessage());
        }
    }

    private static function HandleMapExperienceWorkDetail($data): array
    {
        return array(
            'id' => $data->GetID(),
            'title' => $data->GetTitle(),
            'start_at' => $data->GetStartAt(),
            'end_at' => $data->GetEndAt(),
            'position' => $data->GetPosition(),
            'description' => $data->GetDescription(),
            'achivement' => $data->GetAchivement(),
            'tech' => $data->GetTech()
        );
    }

    public function show(int $id): JsonResponse|ExperienceWorkDomainEntities
    {
        try {
            $data = $this->usecase->show($id);
            if (!$data instanceof JsonResponse) {
                return $this->Response(
                    200,
                    'Berhasil menampilkan detail ExperienceWork',
                    $this->HandleMapExperienceWorkDetail($data),
                );
            }
            return $data;
        } catch (\Exception $error) {
            Log::error("Internal error show api: {$error->getMessage()}");
            return config('app.debug') != true ? $this->Response(500, "Maaf ada kesalahan pada sistem") : $this->Response(500, $error->getMessage());
        }
    }

    public function store(Request $request, ExperienceWorkRequestInfrastructure $validate): JsonResponse
    {
        try {
            #validate request
            $validate = $validate->ValidateExperienceWorkRequest($request);
            if ($validate->fails()) {
                return $this->CustomErrorValidation($validate);
            }

            #save request
            $DTO = new ExperienceWorkDTO(
                $request->post('title') ?? null,
                $request->post('start_at') ?? null,
                $request->post('end_at') ?? null,
                $request->post('position') ?? null,
                $request->post('description') ?? null,
                $request->post('achivement') ?? [],
                $request->post('tech') ?? []
            );

            return $this->usecase->store($DTO);
        } catch (\Exception $error) {
            Log::error("Internal error store api: {$error->getMessage()}");
            return config('app.debug') != true ? $this->Response(500, "Maaf ada kesalahan pada sistem") : $this->Response(500, $error->getMessage());
        }
    }

    public function update(int $id, Request $request, ExperienceWorkRequestInfrastructure $validate): JsonResponse
    {
        try {
            $validate = $validate->ValidateExperienceWorkRequest($request);
            if ($validate->fails()) {
                return $this->CustomErrorValidation($validate);
            }

            $DTO = new ExperienceWorkDTO(
                $request->post('title') ?? null,
                $request->post('start_at') ?? null,
                $request->post('end_at') ?? null,
                $request->post('position') ?? null,
                $request->post('description') ?? null,
                $request->post('achivement') ?? [],
                $request->post('tech') ?? []
            );

            return $this->usecase->update($id, $DTO);
        } catch (\Exception $error) {
            Log::error("Internal error update api: {$error->getMessage()}");
            return config('app.debug') != true ? $this->Response(500, "Maaf ada kesalahan pada sistem") : $this->Response(500, $error->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            return $this->usecase->destroy($id);
        } catch (\Exception $error) {
            Log::error("Internal error delete api: {$error->getMessage()}");
            return config('app.debug') != true ? $this->Response(500, "Maaf ada kesalahan pada sistem") : $this->Response(500, $error->getMessage());
        }
    }
}
