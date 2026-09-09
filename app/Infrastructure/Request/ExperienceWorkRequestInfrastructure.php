<?php

namespace App\Infrastructure\Request;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExperienceWorkRequestInfrastructure
{
    public function ValidateExperienceWorkRequest(Request $request)
    {
        #validate body request only (post)
        return Validator::make($request->request->all(), [
            'title' => 'required|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date',
            'position' => 'required|in:full time,part time,internship',
            'description' => 'required|string',
            'achivement' => 'array',
            'tech' => 'array',
        ]);
    }
}
