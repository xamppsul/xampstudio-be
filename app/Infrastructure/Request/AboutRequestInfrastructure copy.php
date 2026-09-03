<?php

namespace App\Infrastructure\Request;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AboutRequestInfrastructure
{
    public function ValidateAboutRequest(Request $request)
    {

        #validate body request only (post)
        return Validator::make($request->request->all(), [
            'img' => 'string', //gak wajib kalo mau pake gambar silahkan
            'title' => 'required|string',
            'description' => 'required|string',
            'position' => 'required|integer',
            'status' => 'boolean',
        ]);
    }
}
