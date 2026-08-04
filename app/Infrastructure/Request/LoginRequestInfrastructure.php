<?php

namespace App\Infrastructure\Request;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class LoginRequestInfrastructure
{
    public function ValidateLogin(Request $request)
    {
        return Validator::make($request->request->all(), [
            'email' => 'required|email',
            'password' => [
                'required',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ]
        ]);
    }
}
