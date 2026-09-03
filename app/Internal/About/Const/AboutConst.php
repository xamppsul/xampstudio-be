<?php

namespace App\Internal\About\Const;

class AboutConst
{
    const SUCCESSFULLY_LIST = "Berhasil Menampilkan About";
    const SUCCESSFULLY_CREATE = "Berhasil Menambahkan About";
    const SUCCESSFULLY_UPDATE = "Berhasil Mengubah About";
    const SUCCESSFULLY_DELETE = "Berhasil Menghapus About";

    protected static function Response(int $code, string $message = 'No message response', $data = [])
    {
        // switch ($code) {
        //     case $code <= 103:
        //         $data = response()->json([
        //             'status' => $code,
        //             'message' => $message != '' ? $message : 'Informational Purpose Sistem',
        //             'data' => $data
        //         ], $code);
        //         break;
        //     case $code <= 226:
        //         $data = response()->json([
        //             'status' => $code,
        //             'message' => $message != '' ? $message : 'Successfully response',
        //             'data' => $data
        //         ], $code);
        //         break;
        //     case $code <= 308:
        //         $data = response()->json([
        //             'status' => $code,
        //             'message' => $message != '' ? $message : 'Redirection response',
        //             'data' => $data
        //         ], $code);
        //         break;
        //     case $code <= 451:
        //         $data = response()->json([
        //             'status' => $code,
        //             'message' => $message != '' ? $message : 'Error response client',
        //         ], $code);
        //         break;
        //     default:
        //         $data = response()->json([
        //             'status' => $code,
        //             'message' => $message != '' ? $message : 'Internal server error'
        //         ], $code);
        //         break;
        // }
        // return $data;

        if (is_array($data) && empty($data)) {
            return response()->json(['status' => $code, 'message' => $message], $code);
        } else {
            return response()->json(['status' => $code, 'message' => $message, 'data' => $data], $code);
        }
    }

    protected function CustomErrorValidation($data)
    {
        $error = collect($data->errors())->map(function ($message, $field) {
            return [
                'field' => $field,
                'message' => $message[0]
            ];
        })->values();

        return response()->json([
            'status' => 422,
            'message' => 'Data tidak lengkap',
            'data' => $error
        ], 422);
    }
}
