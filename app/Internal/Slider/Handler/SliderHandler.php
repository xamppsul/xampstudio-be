<?php

namespace App\Internal\Slider\Handler;

use App\Domain\Slider\Entities\SliderDomainEntities;
use App\Infrastructure\Lib\Base64Lib;
use App\Infrastructure\Request\SliderRequestInfrastructure;
use App\Internal\Slider\Const\SliderConst;
use App\Internal\Slider\DTO\SliderDTO;
use App\Internal\Slider\Usecase\SliderUsecase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SliderHandler extends SliderConst
{
    private $usecase;
    private $libImg;
    public function __construct(SliderUsecase $usecase, Base64Lib $libImg)
    {
        $this->usecase = $usecase;
        $this->libImg = $libImg;
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

    private static function HandleMapSliderDetail($data): array
    {
        return array(
            'id' => $data->GetID(),
            'img' => $data->GetImg(),
            'title' => $data->GetTitle(),
            'description' => $data->GetDescription(),
            'position' => $data->GetPosition(),
            'status' => $data->GetStatus()
        );
    }

    public function show(int $id): JsonResponse|SliderDomainEntities
    {
        try {
            $data = $this->usecase->show($id);
            if (!$data instanceof JsonResponse) {
                return $this->Response(
                    200,
                    'Berhasil menampilkan detail slider',
                    $this->HandleMapSliderDetail($data),
                );
            }
            return $data;
        } catch (\Exception $error) {
            Log::error("Internal error show api: {$error->getMessage()}");
            return $this->Response(500, $error->getMessage());
        }
    }

    public function store(Request $request, SliderRequestInfrastructure $validate): JsonResponse
    {
        try {
            #validate request
            $validate = $validate->ValidateSliderRequest($request);
            if ($validate->fails()) {
                return $this->CustomErrorValidation($validate);
            }

            #save request
            $DTO = new SliderDTO(
                $request->post('img') ?? null,
                $request->post('title'),
                $request->post('description'),
                $request->post('position'),
                $request->post('status') ?? false #default false event request is empty
            );

            return $this->usecase->store($DTO);
        } catch (\Exception $error) {
            Log::error("Internal error store api: {$error->getMessage()}");
            return $this->Response(500, $error->getMessage());
        }
    }

    public function update(int $id, Request $request, SliderRequestInfrastructure $validate): JsonResponse
    {
        try {
            $validate = $validate->ValidateSliderRequest($request);
            if ($validate->fails()) {
                return $this->CustomErrorValidation($validate);
            }

            $DTO = new SliderDTO(
                $request->post('img') ?? null,
                $request->post('title'),
                $request->post('description'),
                $request->post('position'),
                $request->post('status') ?? false #default false event request is empty
            );

            return $this->usecase->update($id, $DTO);
        } catch (\Exception $error) {
            Log::error("Internal error update api: {$error->getMessage()}");
            return $this->Response(500, $error->getMessage());
        }
    }

    public function destroy()
    {
        try {
        } catch (\Exception $error) {
            Log::error("Internal error delete api: {$error->getMessage()}");
        }
    }
}
