<?php

namespace App\Domain\Slider\Interface;

interface SliderDomainInterface
{
    public function ValidateEmail(string $email): ?User;
    public function ValidatePassword(string $req_password, string $hash_password): Hash|bool;
    public function GenerateSession($user);
}
