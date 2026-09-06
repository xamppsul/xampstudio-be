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
            return $this->repository->GetAboutCollection(
                $request->experience_during,
                $request->project_is_done,
                $request->client_response
            ); #ambil data
        }

        return $this->Response(422, 'Data About belum ada');
    }

    public function show(int $id): JsonResponse|AboutDomainEntities
    {
        if (
            $this->repository->ValidateAboutByID($id) &&
            !is_null($this->repository->GetAboutByID($id))
        ) {
            return new AboutDomainEntities(
                $this->repository->GetAboutByID($id)->id,
                $this->repository->GetAboutByID($id)->core_values
                    ->pluck('name')
                    ->values()
                    ->toArray(),
                $this->repository->GetAboutByID($id)->experience_during,
                $this->repository->GetAboutByID($id)->description,
                $this->repository->GetAboutByID($id)->project_is_done,
                $this->repository->GetAboutByID($id)->client_response,
                $this->repository->GetAboutByID($id)->img,
                $this->repository->GetAboutByID($id)->status
            );
        }

        return $this->Response(422, 'About tidak di temukan atau masih nonaktif');
    }

    public function store(AboutDTO $dto): JsonResponse
    {
        if (empty($dto->img)) {
            #pake gambar default kalo gak upload gambar real
            DB::transaction(function () use ($dto) {
                #insert parent about
                $about = $this->repository->InsertAboutData(
                    $dto,
                    config('app.img_path_not_fund')
                );

                $core_value = [];
                foreach ($dto->core_values as $data) {
                    $core_value[] = [
                        'abouts_id' => $about->id,
                        'name' => $data,
                        'created_at' => now()
                    ];
                }

                #insert chid about of core value
                $this->repository->InsertCoreValuesByAboutID($core_value);
            });
        } else {
            #default: upload gambar real
            #validation base 64 image
            $imgPath = $this->libImg->Index($dto->img, 'about');
            if ($imgPath instanceof JsonResponse) {
                return $imgPath;
            }

            DB::transaction(function () use ($dto, $imgPath) {
                $about = $this->repository->InsertAboutData(
                    $dto,
                    $imgPath
                );

                $core_value = [];
                foreach ($dto->core_values as $data) {
                    $core_value[] = [
                        'abouts_id' => $about->id,
                        'name' => $data,
                        'created_at' => now()
                    ];
                }

                #insert chid about of core value
                $this->repository->InsertCoreValuesByAboutID($core_value);
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
            $imgPath = $this->libImg->Index($dto->img, 'about');
            if ($imgPath instanceof JsonResponse) {
                return $imgPath;
            }

            DB::transaction(function () use ($id, $dto, $imgPath) {
                #core value is array & not null jalankan request
                if (is_array($dto->core_values) && !empty($dto->core_values)) {

                    foreach ($dto->core_values as $cv) {
                        $this->repository->UpdateCoreValueByAboutID(
                            $id,
                            $cv['id'],
                            $cv['name']
                        );
                    }
                }

                $this->repository->UpdateAboutDataWithImg(
                    $id,
                    $dto,
                    $imgPath
                );
            });
        } else {
            #default: gambar tidak berubah
            DB::transaction(function () use ($id, $dto) {
                #core value is array & not null jalankan request
                if (is_array($dto->core_values) && !empty($dto->core_values)) {

                    foreach ($dto->core_values as $cv) {
                        $this->repository->UpdateCoreValueByAboutID(
                            $id,
                            $cv['id'],
                            $cv['name']
                        );
                    }
                }

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
