<?php

namespace App\Internal\Login\Repository;

use App\Domain\Login\Interface\LoginDomainInterface;
use App\Infrastructure\Database\Eloquent\User;
use Illuminate\Support\Facades\Hash;

class LoginRepository implements LoginDomainInterface
{
    public function ValidateEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function ValidatePassword(string $req_password, string $hash_password): Hash|bool
    {
        return !Hash::check($req_password, $hash_password) ? false : true;
    }

    public function GenerateSession($user)
    {
        return $user->createToken('xampstudio')->accessToken;
    }
}
