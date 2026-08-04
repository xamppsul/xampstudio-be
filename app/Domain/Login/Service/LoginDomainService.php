<?php

namespace App\Domain\Login\Service;

use App\Domain\Login\Entities\LoginDomainEntities;
use App\Domain\Login\Interface\LoginDomainInterface;
use App\Internal\Login\Const\LoginConst;
use Illuminate\Http\JsonResponse;

class LoginDomainService extends LoginConst
{
    private $repository;
    public function __construct(LoginDomainInterface $repository)
    {
        $this->repository = $repository;
    }

    public function Login(
        string $email,
        string $password
    ): JsonResponse|LoginDomainEntities {

        #validate user email
        $user = $this->repository->ValidateEmail($email);
        if ($user && $this->repository->ValidatePassword($password, $user->password)) {
            #generate session of user
            $token = $this->repository->GenerateSession($user);

            return new LoginDomainEntities(
                $user->id,
                $user->email,
                $token
            );
        }

        return $this->Response(422, [], "Email anda tidak ditemukan");
    }
}
