<?php

namespace App\Internal\Slider\Handler;

use App\Domain\Slider\Entities\SliderDomainEntities;
use App\Internal\Slider\Const\SliderConst;
use App\Internal\Slider\Usecase\SliderUsecase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SliderHandler extends SliderConst
{
    private $usecase;
    public function __construct(SliderUsecase $usecase)
    {
        $this->usecase = $usecase;
    }

    public function index(Request $request): JsonResponse|Collection|LengthAwarePaginator
    {
        try {
            return $this->usecase->index($request);
        } catch (\Exception $error) {
            Log::error("Internal error index api: {$error->getMessage()}");
            return $this->Response(500, [], $error->getMessage());
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
                    $this->HandleMapSliderDetail($data),
                    'Berhasil menampilkan detail slider'
                );
            }
            return $data;
        } catch (\Exception $error) {
            Log::error("Internal error show api: {$error->getMessage()}");
            return $this->Response(500, [], $error->getMessage());
        }
    }

    public function store()
    {
        try {
        } catch (\Exception $error) {
            Log::error("Internal error store api: {$error->getMessage()}");
        }
    }

    public function update()
    {
        try {
        } catch (\Exception $error) {
            Log::error("Internal error update api: {$error->getMessage()}");
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
