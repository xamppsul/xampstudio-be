<?php

namespace App\Internal\Slider\Handler;

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

    public function show(int $id)
    {
        try {
        } catch (\Exception $error) {
            Log::error("Internal error show api: {$error->getMessage()}");
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
