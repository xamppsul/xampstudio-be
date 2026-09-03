<?php

namespace App\Domain\About\Service;

use App\Domain\About\Entities\AboutDomainEntities;
use App\Domain\About\Interface\AboutDomainInterface;
use App\Infrastructure\Database\Eloquent\About;
use App\Infrastructure\Lib\Base64Lib;
use App\Internal\About\Const\AboutConst;
use App\Internal\About\DTO\AboutDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AboutDomainService extends AboutConst
{
    private $repository;
    private $libImg;
    public function __construct(AboutDomainInterface $repository, Base64Lib $libImg)
    {
        $this->repository = $repository;
        $this->libImg = $libImg;
    }

    public function index($request): JsonResponse|Collection|LengthAwarePaginator
    {
        $data = $this->repository->ValidateAboutCollection(); #default [] jikalau data kosong
        if ($data->isNotEmpty()) {
            return $this->repository->GetAboutCollection($request->title, $request->date); #ambil data
        }

        return $this->Response(422, 'Data About belum ada');
    }

    public function show(int $id): JsonResponse|AboutDomainEntities
    {

        if (!$this->repository->ValidateAboutByID($id)) {
            return $this->Response(422, 'About tidak di temukan');
        }

        return new AboutDomainEntities(
            $this->repository->GetAboutByID($id)->id,
            $this->repository->GetAboutByID($id)->img,
            $this->repository->GetAboutByID($id)->title,
            $this->repository->GetAboutByID($id)->description,
            $this->repository->GetAboutByID($id)->position,
            $this->repository->GetAboutByID($id)->status
        );
    }

    public function store(AboutDTO $dto)
    {
        if (empty($dto->img)) {
            #pake gambar default kalo gak upload gambar real
            DB::transaction(function () use ($dto) {
                $this->repository->InsertAboutData(
                    $dto,
                    config('app.img_path_not_fund')
                );
            });
        } else {
            #default: upload gambar real
            #validation base 64 image
            $imgPath = $this->libImg->Index($dto->img, 'About');
            if ($imgPath instanceof JsonResponse) {
                return $imgPath;
            }
            DB::transaction(function () use ($dto, $imgPath) {
                $this->repository->InsertAboutData(
                    $dto,
                    $imgPath
                );
            });
        }
        return $this->Response(200, 'Berhasil tambah About');
    }

    public function update(int $id, AboutDTO $dto): JsonResponse
    {
        if (!$this->repository->ValidateAboutByID($id)) {
            return $this->Response(422, 'About tidak di temukan');
        }

        if (!empty($dto->img)) {
            #request up gambar then upload real img to s3
            $imgPath = $this->libImg->Index($dto->img, 'About');
            if ($imgPath instanceof JsonResponse) {
                return $imgPath;
            }

            DB::transaction(function () use ($id, $dto, $imgPath) {
                $this->repository->UpdateAboutDataWithImg(
                    $id,
                    $dto,
                    $imgPath
                );
            });
        } else {
            #default: gambar tidak berubah
            DB::transaction(function () use ($id, $dto) {
                $this->repository->UpdateAboutDataNoImg(
                    $id,
                    $dto
                );
            });
        }


        return $this->Response(200, 'Berhasil ubah About');
    }

    public function destroy(int $id): JsonResponse
    {
        if (!$this->repository->ValidateAboutByID($id)) {
            return $this->Response(422, 'About tidak di temukan');
        }

        $this->repository->DeleteAboutData($id);
        return $this->Response(200, 'Berhasil delete About');
    }
}
