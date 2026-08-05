<?php

namespace App\Internal\Slider\Repository;

use App\Domain\Slider\Interface\SliderDomainInterface;
use Illuminate\Support\Facades\Hash;

class SliderRepository implements SliderDomainInterface
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
