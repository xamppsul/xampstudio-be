<?php

namespace App\Internal\ExperienceWork\Usecase;

use App\Domain\ExperienceWork\Entities\ExperienceWorkDomainEntities;
use App\Domain\ExperienceWork\Service\ExperienceWorkDomainService;
use App\Internal\ExperienceWork\DTO\ExperienceWorkDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class ExperienceWorkUsecase
{
    private $service;
    public function __construct(ExperienceWorkDomainService $service)
    {
        $this->service = $service;
    }

    #interact with domain service ExperienceWork:index
    public function index($request): JsonResponse|Collection|LengthAwarePaginator
    {
        return $this->service->index($request);
    }

    #interact with domain service ExperienceWork:show
    public function show(int $id): JsonResponse|ExperienceWorkDomainEntities
    {
        return $this->service->show($id);
    }

    #interact with domain service ExperienceWork:store
    public function store(ExperienceWorkDTO $dto): JsonResponse
    {
        return $this->service->store($dto);
    }

    #interact with domain service ExperienceWork:update
    public function update(int $id, ExperienceWorkDTO $dto): JsonResponse
    {
        return $this->service->update($id, $dto);
    }

    #interact with domain service ExperienceWork:delete
    public function destroy(int $id): JsonResponse
    {
        return $this->service->destroy($id);
    }
}
