<?php

namespace App\Internal\Login\Const;

class LoginConst
{
    const ERROR_VALIDATION = "Terjadi kesalah validasi mohon di perbaiki";

    protected static function Response(int $code, string $message = '', $data = [])
    {

        switch ($code) {
            case $code <= 103:
                $data = response()->json([
                    'status' => $code,
                    'message' => $message != '' ? $message : 'Informational Purpose Sistem',
                    'data' => $data
                ]);
                break;
            case $code <= 226:
                $data = response()->json([
                    'status' => $code,
                    'message' => $message != '' ? $message : 'Successfully response'
                ]);
                break;
            case $code <= 308:
                $data = response()->json([
                    'status' => $code,
                    'message' => $message != '' ? $message : 'Redirection response'
                ]);
                break;
            case $code <= 451:
                $data = response()->json([
                    'status' => $code,
                    'message' => $message != '' ? $message : 'Error response client'
                ]);
                break;
            default:
                $data = response()->json([
                    'status' => $code,
                    'message' => $message != '' ? $message : 'Internal server error'
                ]);
                break;
        }
        return $data;
    }
}
