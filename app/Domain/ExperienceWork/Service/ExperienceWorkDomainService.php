<?php

namespace App\Domain\ExperienceWork\Service;

use App\Domain\ExperienceWork\Entities\ExperienceWorkDomainEntities;
use App\Domain\ExperienceWork\Interface\ExperienceWorkDomainInterface;
use App\Infrastructure\Lib\Base64Lib;
use App\Internal\ExperienceWork\Const\ExperienceWorkConst;
use App\Internal\ExperienceWork\DTO\ExperienceWorkDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ExperienceWorkDomainService extends ExperienceWorkConst
{
    private $repository;
    private $libImg;
    public function __construct(ExperienceWorkDomainInterface $repository, Base64Lib $libImg)
    {
        $this->repository = $repository;
        $this->libImg = $libImg;
    }

    public function index($request): JsonResponse|Collection|LengthAwarePaginator
    {
        $data = $this->repository->ValidateExperienceWorkCollection(); #default [] jikalau data kosong
        if ($data->isNotEmpty()) {
            return $this->repository->GetExperienceWorkCollection(
                $request->experience_during,
                $request->project_is_done,
                $request->client_response
            ); #ambil data
        }

        return $this->Response(422, 'Data ExperienceWork belum ada');
    }

    public function show(int $id): JsonResponse|ExperienceWorkDomainEntities
    {
        if (
            $this->repository->ValidateExperienceWorkByID($id) &&
            !is_null($this->repository->GetExperienceWorkByID($id))
        ) {
            return new ExperienceWorkDomainEntities(
                $this->repository->GetExperienceWorkByID($id)->id,
                $this->repository->GetExperienceWorkByID($id)->core_values
                    ->pluck('name')
                    ->values()
                    ->toArray(),
                $this->repository->GetExperienceWorkByID($id)->experience_during,
                $this->repository->GetExperienceWorkByID($id)->description,
                $this->repository->GetExperienceWorkByID($id)->project_is_done,
                $this->repository->GetExperienceWorkByID($id)->client_response,
                $this->repository->GetExperienceWorkByID($id)->img,
                $this->repository->GetExperienceWorkByID($id)->status
            );
        }

        return $this->Response(422, 'ExperienceWork tidak di temukan atau masih nonaktif');
    }

    public function store(ExperienceWorkDTO $dto): JsonResponse
    {
        if (empty($dto->img)) {
            #pake gambar default kalo gak upload gambar real
            DB::transaction(function () use ($dto) {
                #insert parent ExperienceWork
                $ExperienceWork = $this->repository->InsertExperienceWorkData(
                    $dto,
                    config('app.img_path_not_fund')
                );

                $core_value = [];
                foreach ($dto->core_values as $data) {
                    $core_value[] = [
                        'ExperienceWorks_id' => $ExperienceWork->id,
                        'name' => $data,
                        'created_at' => now()
                    ];
                }

                #insert chid ExperienceWork of core value
                $this->repository->InsertCoreValuesByExperienceWorkID($core_value);
            });
        } else {
            #default: upload gambar real
            #validation base 64 image
            $imgPath = $this->libImg->Index($dto->img, 'ExperienceWork');
            if ($imgPath instanceof JsonResponse) {
                return $imgPath;
            }

            DB::transaction(function () use ($dto, $imgPath) {
                $ExperienceWork = $this->repository->InsertExperienceWorkData(
                    $dto,
                    $imgPath
                );

                $core_value = [];
                foreach ($dto->core_values as $data) {
                    $core_value[] = [
                        'ExperienceWorks_id' => $ExperienceWork->id,
                        'name' => $data,
                        'created_at' => now()
                    ];
                }

                #insert chid ExperienceWork of core value
                $this->repository->InsertCoreValuesByExperienceWorkID($core_value);
            });
        }
        return $this->Response(200, 'Berhasil tambah ExperienceWork');
    }

    public function update(int $id, ExperienceWorkDTO $dto): JsonResponse
    {
        if (!$this->repository->ValidateExperienceWorkByID($id)) {
            return $this->Response(422, 'ExperienceWork tidak di temukan');
        }

        if (!empty($dto->img)) {

            #request up gambar then upload real img to s3
            $imgPath = $this->libImg->Index($dto->img, 'ExperienceWork');
            if ($imgPath instanceof JsonResponse) {
                return $imgPath;
            }

            DB::transaction(function () use ($id, $dto, $imgPath) {
                #core value is array & not null jalankan request
                if (is_array($dto->core_values) && !empty($dto->core_values)) {

                    foreach ($dto->core_values as $cv) {
                        $this->repository->UpdateCoreValueByExperienceWorkID(
                            $id,
                            $cv['id'],
                            $cv['name']
                        );
                    }
                }

                $this->repository->UpdateExperienceWorkDataWithImg(
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
                        $this->repository->UpdateCoreValueByExperienceWorkID(
                            $id,
                            $cv['id'],
                            $cv['name']
                        );
                    }
                }

                $this->repository->UpdateExperienceWorkDataNoImg(
                    $id,
                    $dto
                );
            });
        }


        return $this->Response(200, 'Berhasil ubah ExperienceWork');
    }

    public function destroy(int $id): JsonResponse
    {
        if (!$this->repository->ValidateExperienceWorkByID($id)) {
            return $this->Response(422, 'ExperienceWork tidak di temukan');
        }

        $this->repository->DeleteCoreValuesByExperienceWorksID($id);
        $this->repository->DeleteExperienceWorkData($id);
        return $this->Response(200, 'Berhasil delete ExperienceWork');
    }
}
