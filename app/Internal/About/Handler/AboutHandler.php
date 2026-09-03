<?php

namespace App\Internal\About\Handler;

use App\Domain\About\Entities\AboutDomainEntities;
use App\Infrastructure\Request\AboutRequestInfrastructure;
use App\Internal\About\Const\AboutConst;
use App\Internal\About\DTO\AboutDTO;
use App\Internal\About\Usecase\AboutUsecase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AboutHandler extends AboutConst
{
    private $usecase;
    public function __construct(AboutUsecase $usecase)
    {
        $this->usecase = $usecase;
    }

    public function index(Request $request): JsonResponse|Collection|LengthAwarePaginator
    {
        try {
            return $this->usecase->index($request);
        } catch (\Exception $error) {
            Log::error("Internal error index api: {$error->getMessage()}");
            return $this->Response(500, $error->getMessage());
        }
    }

    private static function HandleMapAboutDetail($data): array
    {
        return array(
            'id' => $data->GetID(),
            'core_value' => $data->GetCoreValuesID(),
            'experience_during' => $data->GetExperienceDuring(),
            'description' => $data->GetDescription(),
            'project_is_done' => $data->GetProjectIsDone(),
            'client_response' => $data->GetClientResponse(),
            'img' => $data->GetImg()
        );
    }

    public function show(int $id): JsonResponse|AboutDomainEntities
    {
        try {
            $data = $this->usecase->show($id);
            if (!$data instanceof JsonResponse) {
                return $this->Response(
                    200,
                    'Berhasil menampilkan detail About',
                    $this->HandleMapAboutDetail($data),
                );
            }
            return $data;
        } catch (\Exception $error) {
            Log::error("Internal error show api: {$error->getMessage()}");
            return $this->Response(500, $error->getMessage());
        }
    }

    public function store(Request $request, AboutRequestInfrastructure $validate): JsonResponse
    {
        try {
            #validate request
            $validate = $validate->ValidateAboutRequest($request);
            if ($validate->fails()) {
                return $this->CustomErrorValidation($validate);
            }

            #save request
            $DTO = new AboutDTO(
                $request->post('core_values') ?? [],
                $request->post('experience_during'),
                $request->post('description'),
                $request->post('project_is_done'),
                $request->post('client_response'),
                $request->post('img') ?? null
            );

            return $this->usecase->store($DTO);
        } catch (\Exception $error) {
            Log::error("Internal error store api: {$error->getMessage()}");
            return $this->Response(500, $error->getMessage());
        }
    }

    public function update(int $id, Request $request, AboutRequestInfrastructure $validate): JsonResponse
    {
        try {
            $validate = $validate->ValidateAboutRequest($request);
            if ($validate->fails()) {
                return $this->CustomErrorValidation($validate);
            }

            $DTO = new AboutDTO(
                $request->post('core_values') ?? [],
                $request->post('experience_during'),
                $request->post('description'),
                $request->post('project_is_done'),
                $request->post('client_response'),
                $request->post('img') ?? null
            );

            return $this->usecase->update($id, $DTO);
        } catch (\Exception $error) {
            Log::error("Internal error update api: {$error->getMessage()}");
            return $this->Response(500, $error->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            return $this->usecase->destroy($id);
        } catch (\Exception $error) {
            Log::error("Internal error delete api: {$error->getMessage()}");
            return $this->Response(500, $error->getMessage());
        }
    }
}
