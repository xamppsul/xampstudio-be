<?php

namespace App\Domain\Slider\Service;

use App\Domain\Slider\Entities\SliderDomainEntities;
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

    public function show(int $id): JsonResponse|SliderDomainEntities
    {

        if (!$this->repository->ValidateSliderByID($id)) {
            return $this->Response(422, [], 'Slider tidak di temukan');
        }

        return new SliderDomainEntities(
            $this->repository->GetSliderByID($id)->id,
            $this->repository->GetSliderByID($id)->img,
            $this->repository->GetSliderByID($id)->title,
            $this->repository->GetSliderByID($id)->description,
            $this->repository->GetSliderByID($id)->position,
            $this->repository->GetSliderByID($id)->status
        );
    }
}
