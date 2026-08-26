<?php

namespace App\Domain\Slider\Service;

use App\Domain\Slider\Entities\SliderDomainEntities;
use App\Domain\Slider\Interface\SliderDomainInterface;
use App\Infrastructure\Database\Eloquent\Slider;
use App\Infrastructure\Lib\Base64Lib;
use App\Internal\Slider\Const\SliderConst;
use App\Internal\Slider\DTO\SliderDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SliderDomainService extends SliderConst
{
    private $repository;
    private $libImg;
    public function __construct(SliderDomainInterface $repository, Base64Lib $libImg)
    {
        $this->repository = $repository;
        $this->libImg = $libImg;
    }

    public function index($request): JsonResponse|Collection|LengthAwarePaginator
    {
        $data = $this->repository->ValidateSliderCollection(); #default [] jikalau data kosong
        if ($data->isNotEmpty()) {
            return $this->repository->GetSliderCollection($request->title, $request->date); #ambil data
        }

        return $this->Response(422, 'Data slider belum ada');
    }

    public function show(int $id): JsonResponse|SliderDomainEntities
    {

        if (!$this->repository->ValidateSliderByID($id)) {
            return $this->Response(422, 'Slider tidak di temukan');
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

    public function store(SliderDTO $dto)
    {
        if (empty($dto->img)) {
            #pake gambar default kalo gak upload gambar real
            DB::transaction(function () use ($dto) {
                $this->repository->InsertSliderData(
                    $dto,
                    config('app.img_path_not_fund')
                );
            });
        } else {
            #default: upload gambar real
            #validation base 64 image
            $imgPath = $this->libImg->Index($dto->img, 'slider');
            if ($imgPath instanceof JsonResponse) {
                return $imgPath;
            }
            DB::transaction(function () use ($dto, $imgPath) {
                $this->repository->InsertSliderData(
                    $dto,
                    $imgPath
                );
            });
        }
        return $this->Response(200, 'Berhasil tambah slider');
    }

    public function update(int $id, SliderDTO $dto): JsonResponse
    {
        if (!$this->repository->ValidateSliderByID($id)) {
            return $this->Response(422, 'Slider tidak di temukan');
        }

        if (!empty($dto->img)) {
            #request up gambar then upload real img to s3
            $imgPath = $this->libImg->Index($dto->img, 'slider');
            if ($imgPath instanceof JsonResponse) {
                return $imgPath;
            }

            DB::transaction(function () use ($id, $dto, $imgPath) {
                $this->repository->UpdateSliderDataWithImg(
                    $id,
                    $dto,
                    $imgPath
                );
            });
        } else {
            #default: gambar tidak berubah
            DB::transaction(function () use ($id, $dto) {
                $this->repository->UpdateSliderDataNoImg(
                    $id,
                    $dto
                );
            });
        }


        return $this->Response(200, 'Berhasil ubah slider');
    }
}
