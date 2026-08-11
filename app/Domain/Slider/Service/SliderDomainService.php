<?php

namespace App\Domain\Slider\Service;

use App\Domain\Slider\Entities\SliderDomainEntities;
use App\Domain\Slider\Interface\SliderDomainInterface;
use App\Infrastructure\Database\Eloquent\Slider;
use App\Infrastructure\Lib\Base64Lib;
use App\Internal\Login\Const\LoginConst;
use App\Internal\Slider\DTO\SliderDTO;
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

    public function store(SliderDTO $dto, string $base64ImageSlider): JsonResponse|Slider
    {

        #instance local instance on object of class base64 lib
        $base64 = new Base64Lib();
        $path = $base64->Index($base64ImageSlider, 'slider'); #return path img after upload s3 aws
        if (!$path instanceof JsonResponse) {
            return $this->repository->InsertSliderData($dto, $path);
        }

        return $path; #default is return json response event error validation base64 Image

    }
}
