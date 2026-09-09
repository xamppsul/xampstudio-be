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
            'core_value' => $data->GetCoreValuesID(),
            'experience_during' => $data->GetExperienceDuring(),
            'description' => $data->GetDescription(),
            'project_is_done' => $data->GetProjectIsDone(),
            'client_response' => $data->GetClientResponse(),
            'img' => $data->GetImg(),
            'status' => $data->GetStatus()
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
                $request->post('core_values') ?? [],
                $request->post('experience_during'),
                $request->post('description') ?? null,
                $request->post('project_is_done') ?? 0,
                $request->post('client_response') ?? 0,
                $request->post('img') ?? null,
                $request->post('status') ?? false
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
                $request->post('core_values') ?? [],
                $request->post('experience_during'),
                $request->post('description'),
                $request->post('project_is_done'),
                $request->post('client_response'),
                $request->post('img') ?? null,
                $request->post('status') ?? false
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
