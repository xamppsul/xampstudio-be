<?php

namespace App\Internal\Slider\Usecase;

use App\Domain\Slider\Entities\SliderDomainEntities;
use App\Domain\Slider\Service\SliderDomainService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class SliderUsecase
{
    private $service;
    public function __construct(SliderDomainService $service)
    {
        $this->service = $service;
    }

    #interact with domain service slider:index
    public function index($request): JsonResponse|Collection|LengthAwarePaginator
    {
        return $this->service->index($request);
    }

    #interact with domain service slider:show
    public function show(int $id): JsonResponse|SliderDomainEntities
    {
        return $this->service->show($id);
    }

    #interact with domain service slider:store
    public function store() {}

    #interact with domain service slider:update
    public function update() {}

    #interact with domain service slider:delete
    public function destroy() {}
}
