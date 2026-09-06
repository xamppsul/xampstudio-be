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
            'core_values' => 'array', //gak wajib kalo mau pake gambar silahkan
            'experience_during' => 'required|int',
            'description' => 'required|string',
            'project_is_done' => 'required|integer',
            'client_response' => 'required|integer',
            'img' => 'string',
        ]);
    }
}
