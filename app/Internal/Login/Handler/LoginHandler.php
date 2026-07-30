<?php

namespace App\Internal\Login\Handler;

use App\Infrastructure\Request\LoginRequestInfrastructure;
use App\Internal\Login\Const\LoginConst;
use App\Internal\Login\Usecase\LoginUsecase;
use Illuminate\Http\Request;

class LoginHandler extends LoginConst
{

    private $usecase;
    public function __construct(LoginUsecase $usecase)
    {
        $this->usecase = $usecase;
    }

    public function login(Request $request, LoginRequestInfrastructure $validate)
    {
        $validate = $validate->ValidateLogin($request);
        if ($validate->fails()) {
            return $this->Response(422);
        }

        return 'hello';
    }
}
