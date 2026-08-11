<?php

namespace App\Infrastructure\Request;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SliderRequestInfrastructure
{
    public function ValidateSliderRequest(Request $request)
    {

        #validate body request only
        return Validator::make($request->request->all(), [
            'img' => 'required|string',
            'title' => 'required|string',
            'description' => 'required|string',
            'position' => 'required|integer',
            'status' => 'boolean',
        ]);
    }
}
