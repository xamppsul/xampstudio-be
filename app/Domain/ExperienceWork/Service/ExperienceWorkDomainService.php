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
                $request->query('title'),
                $request->query('start_at'),
                $request->query('end_at'),
                $request->query('position')
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
                $this->repository->GetExperienceWorkByID($id)->start_at,
                $this->repository->GetExperienceWorkByID($id)->end_at,
                $this->repository->GetExperienceWorkByID($id)->position,
                $this->repository->GetExperienceWorkByID($id)->description,
                $this->repository->GetExperienceWorkByID($id)->achivement
                    ->pluck('achive')
                    ->values()
                    ->toArray(),
                $this->repository->GetExperienceWorkByID($id)->techstack
                    ->pluck('techstacks_id')
                    ->values()
                    ->toArray(),
            );
        }

        return $this->Response(422, 'ExperienceWork tidak di temukan atau masih nonaktif');
    }

    public function store(ExperienceWorkDTO $dto): JsonResponse
    {

        /**
         * 1. cek end_at dikirim atau tidak(optional)
         * 2. jika di kirim maka jalankan rule waktu mulai dan waktu akhir
         * 3. jika waktu mulai dan waktu akhir tidak sesuai rule yang ditetapkan maka error input
         * 4. tujuan dari rule validasi start_at and end_at agar input sesuai (waktu awal lebih kecil dari waktu akhir)
         */
        if ($this->repository->ValidateEndAtIsExists($dto)) {

            if (!$this->repository->ValidateRuleSetDateExperienceWork($dto)) {
                return $this->Response(422, 'Set waktu mulai dan akhir masih salah');
            }
        }

        DB::transaction(function () use ($dto) {
            #insert parent ExperienceWork
            $ExperienceWork = $this->repository->InsertExperienceWorkData(
                $dto,
            );

            #insert chid ExperienceWork of achivement
            $achive = [];
            foreach ($dto->achivement as $data) {
                $achive[] = [
                    'experience_works_id' => $ExperienceWork->id,
                    'achive' => $data,
                    'created_at' => now()
                ];
            }

            $this->repository->InsertAchivementByExperienceWorkID($achive);

            #insert chid ExperienceWork of achivement
            $techs = [];
            foreach ($dto->tech as $data) {
                $techs[] = [
                    'experience_works_id' => $ExperienceWork->id,
                    'techstacks_id' => $data,
                    'created_at' => now()
                ];
            }

            $this->repository->InsertTechStackByExperienceWorkID($techs);
        });
        return $this->Response(200, 'Berhasil tambah ExperienceWork');
    }

    public function update(int $id, ExperienceWorkDTO $dto): JsonResponse
    {
        if (!$this->repository->ValidateExperienceWorkByID($id)) {
            return $this->Response(422, 'ExperienceWork tidak di temukan');
        }

        // if (!empty($dto->img)) {

        //     #request up gambar then upload real img to s3
        //     $imgPath = $this->libImg->Index($dto->img, 'ExperienceWork');
        //     if ($imgPath instanceof JsonResponse) {
        //         return $imgPath;
        //     }

        //     DB::transaction(function () use ($id, $dto, $imgPath) {
        //         #core value is array & not null jalankan request
        //         if (is_array($dto->core_values) && !empty($dto->core_values)) {

        //             foreach ($dto->core_values as $cv) {
        //                 $this->repository->UpdateCoreValueByExperienceWorkID(
        //                     $id,
        //                     $cv['id'],
        //                     $cv['name']
        //                 );
        //             }
        //         }

        //         $this->repository->UpdateExperienceWorkDataWithImg(
        //             $id,
        //             $dto,
        //             $imgPath
        //         );
        //     });
        // } else {
        //     #default: gambar tidak berubah
        //     DB::transaction(function () use ($id, $dto) {
        //         #core value is array & not null jalankan request
        //         if (is_array($dto->core_values) && !empty($dto->core_values)) {

        //             foreach ($dto->core_values as $cv) {
        //                 $this->repository->UpdateCoreValueByExperienceWorkID(
        //                     $id,
        //                     $cv['id'],
        //                     $cv['name']
        //                 );
        //             }
        //         }

        //         $this->repository->UpdateExperienceWorkDataNoImg(
        //             $id,
        //             $dto
        //         );
        //     });
        // }


        return $this->Response(200, 'Berhasil ubah ExperienceWork');
    }

    public function destroy(int $id): JsonResponse
    {
        if (!$this->repository->ValidateExperienceWorkByID($id)) {
            return $this->Response(422, 'ExperienceWork tidak di temukan');
        }

        #child experience work:achivement,tech
        $this->repository->DeleteAchivementByExperienceWorksID($id);
        $this->repository->DeleteTechByExperienceWorksID($id);

        #parent experience work
        $this->repository->DeleteExperienceWorkData($id);
        return $this->Response(200, 'Berhasil delete ExperienceWork');
    }
}
