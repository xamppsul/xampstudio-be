<?php

namespace App\Domain\Login\Interface;

use App\Infrastructure\Database\Eloquent\User;
use Illuminate\Support\Facades\Hash;

interface LoginDomainInterface
{
    public function ValidateEmail(string $email): ?User;
    public function ValidatePassword(string $req_password, string $hash_password): Hash|bool;
    public function GenerateSession($user);
}
