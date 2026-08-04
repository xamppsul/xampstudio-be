<?php

namespace App\Internal\Login\Usecase;

use App\Domain\Login\Entities\LoginDomainEntities;
use App\Domain\Login\Service\LoginDomainService;
use App\Internal\Login\DTO\LoginDTO;
use Illuminate\Http\JsonResponse;

class LoginUsecase
{
    private $service;
    public function __construct(LoginDomainService $service)
    {
        $this->service = $service;
    }

    #interaction with domain and send dto:login service
    public function Login(LoginDTO $dto): JsonResponse|LoginDomainEntities
    {
        return $this->service->Login(
            $dto->email,
            $dto->password
        );
    }
}
