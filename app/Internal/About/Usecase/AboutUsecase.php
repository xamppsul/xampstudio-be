<?php

namespace App\Internal\About\Usecase;

use App\Domain\About\Entities\AboutDomainEntities;
use App\Domain\About\Service\AboutDomainService;
use App\Infrastructure\Database\Eloquent\About;
use App\Internal\About\DTO\AboutDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class AboutUsecase
{
    private $service;
    public function __construct(AboutDomainService $service)
    {
        $this->service = $service;
    }

    #interact with domain service about:index
    public function index($request): JsonResponse|Collection|LengthAwarePaginator
    {
        return $this->service->index($request);
    }

    #interact with domain service about:show
    public function show(int $id): JsonResponse|AboutDomainEntities
    {
        return $this->service->show($id);
    }

    #interact with domain service about:store
    public function store(AboutDTO $dto): JsonResponse
    {
        return $this->service->store($dto);
    }

    #interact with domain service about:update
    public function update(int $id, AboutDTO $dto): JsonResponse
    {
        return $this->service->update($id, $dto);
    }

    #interact with domain service about:delete
    public function destroy(int $id): JsonResponse
    {
        return $this->service->destroy($id);
    }
}
