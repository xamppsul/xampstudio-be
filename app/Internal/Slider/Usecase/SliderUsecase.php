<?php

namespace App\Internal\Slider\Usecase;

use App\Domain\Slider\Service\SliderDomainService;
use Illuminate\Http\JsonResponse;

class SliderUsecase
{
    private $service;
    public function __construct(SliderDomainService $service)
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
