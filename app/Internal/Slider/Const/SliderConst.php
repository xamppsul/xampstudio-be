<?php

namespace App\Internal\Slider\Const;

class SliderConst
{
    const SUCCESSFULLY_LIST = "Berhasil Menampilkan SLider";
    const SUCCESSFULLY_CREATE = "Berhasil Menambahkan SLider";
    const SUCCESSFULLY_UPDATE = "Berhasil Mengubah SLider";
    const SUCCESSFULLY_DELETE = "Berhasil Menghapus SLider";

    protected static function Response(int $code, $data = [], string $message = '')
    {
        switch ($code) {
            case $code <= 103:
                $data = response()->json([
                    'status' => $code,
                    'message' => $message != '' ? $message : 'Informational Purpose Sistem',
                    'data' => $data
                ], $code);
                break;
            case $code <= 226:
                $data = response()->json([
                    'status' => $code,
                    'message' => $message != '' ? $message : 'Successfully response',
                    'data' => $data
                ], $code);
                break;
            case $code <= 308:
                $data = response()->json([
                    'status' => $code,
                    'message' => $message != '' ? $message : 'Redirection response',
                    'data' => $data
                ], $code);
                break;
            case $code <= 451:
                $data = response()->json([
                    'status' => $code,
                    'message' => $message != '' ? $message : 'Error response client',
                ], $code);
                break;
            default:
                $data = response()->json([
                    'status' => $code,
                    'message' => $message != '' ? $message : 'Internal server error'
                ], $code);
                break;
        }
        return $data;
    }
}
