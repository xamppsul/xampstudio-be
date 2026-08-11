<?php

namespace App\Infrastructure\Lib;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Base64Lib
{
    #should be 1MB
    const int MAX_SIZE_IN_BYTES = 5 * 512 * 512;

    /**
     * @method HandleValidateBase64Format()
     * @param $image <- get argumen value of base 64
     * @return mixed
     */
    private static function HandleValidateBase64Format(string $image): mixed
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $image, $matches)) {
            return false;
        }

        return $matches;
    }

    /**
     * @method HandleValidateExtensionAllow()
     * @param string $extension <- get argumen value read image extension with exec from base64
     * @return bool
     */
    private static function HandleValidateExtensionAllow(string $extension): bool
    {
        $allowedExtensions = ['jpeg', 'jpg', 'png', 'webp']; #image type
        return !in_array($extension, $allowedExtensions) ? false : true;
    }

    /**
     * @method HandleValidateBase64HasDecode()
     * @param $decodeImage <- get argumen value of base64 format has decode
     * @return bool
     */
    private static function HandleValidateBase64HasDecode(bool $decodeImage): bool
    {
        return $decodeImage === false ? true : false;
    }

    private static function HandleValidateSizeMaxUploadImageBase64(string $decodeImage, int $maxSize): bool
    {
        return (strlen($decodeImage) > $maxSize) ? true : false;
    }

    /**
     * @method Index()
     * @param string $base64Image <- from body request only: should be base64 format
     */
    public function Index(string $base64Image, string $folder): JsonResponse|string
    {
        #validate format base64 nya apakah valid?
        $matches = $this->HandleValidateBase64Format($base64Image);
        if (!$matches) {
            return response()->json([
                'status' => 422,
                'message' => 'Format base64 image tidak valid',
            ], 422);
        };

        #validasi extension image yang di izinkan
        $extension = strtolower($matches[1]);
        if (!$this->HandleValidateExtensionAllow($extension)) {
            return response()->json([
                'status' => 422,
                'message' => 'Tipe gambar tidak diizinkan',
            ], 422);
        }

        #remove prefix "data:image/xxx:base64," then decode
        $imageData = substr($base64Image, strpos($base64Image, ',') + 1);
        $decodeImage = base64_decode($imageData);
        if ($this->HandleValidateBase64HasDecode($decodeImage)) {
            return response()->json([
                'status' => 422,
                'message' => 'Gagal decode base64',
            ], 422);
        }

        #event has decode then validate size upload image (max 1MB)
        $maxSize = static::MAX_SIZE_IN_BYTES;
        if ($this->HandleValidateSizeMaxUploadImageBase64($decodeImage, $maxSize)) {
            return response()->json([
                'status' => 422,
                'message' => 'Ukuran gambar melebihi 5MB',
            ], 422);
        }

        #generate filename img unique as uuid
        $fileName = Str::uuid() . '.' . $extension;
        $path = "{$folder}/{$fileName}";

        #upload ke s3
        Storage::disk('s3')->put($path, $decodeImage, 'public');

        #ambil url publik
        $url = Storage::disk('s3')->url($path);

        return $url;
    }
}
