<?php

namespace App\Domain\Slider\Service;

use App\Domain\Slider\Interface\SliderDomainInterface;
use App\Internal\Login\Const\LoginConst;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class SliderDomainService extends LoginConst
{
    private $repository;
    public function __construct(SliderDomainInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index($request): JsonResponse|Collection|LengthAwarePaginator
    {
        $data = $this->repository->ValidateSliderCollection(); #default [] jikalau data kosong
        if ($data->isNotEmpty()) {
            return $this->repository->GetSliderCollection($request->title, $request->date); #ambil data
        }

        return $this->Response(422, [], 'Data slider belum ada');
    }
}
