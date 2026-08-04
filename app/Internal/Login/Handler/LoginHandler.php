<?php

namespace App\Internal\Login\Handler;

use App\Domain\Login\Entities\LoginDomainEntities;
use App\Infrastructure\Request\LoginRequestInfrastructure;
use App\Internal\Login\Const\LoginConst;
use App\Internal\Login\DTO\LoginDTO;
use App\Internal\Login\Usecase\LoginUsecase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoginHandler extends LoginConst
{
    private $usecase;
    public function __construct(LoginUsecase $usecase)
    {
        $this->usecase = $usecase;
    }

    public function login(Request $request, LoginRequestInfrastructure $validate): JsonResponse|LoginDomainEntities
    {
        try {
            #validate request body
            $validate = $validate->ValidateLogin($request);
            if ($validate->fails()) {
                return $this->Response(422, $validate->errors());
            }

            #store data in DTO
            $DTO = new LoginDTO(
                $request->email,
                $request->password
            );

            #exec usecase and send DTO
            $data = $this->usecase->Login($DTO);
            if (!$data instanceof JsonResponse) {

                #entity return if valid credential login
                return $this->Response(200, array(
                    'id' => $data->GetID(),
                    'email' => $data->GetEmail(),
                    'token' => $data->GetToken()
                ), self::LOGIN_SUCCESS);
            }

            #default json response event error credential login
            return $data;
        } catch (\Exception $error) {
            Log::error("internal error: {$error->getMessage()}");
            return $this->Response(500, [], $error->getMessage());
        }
    }
}
