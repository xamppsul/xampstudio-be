<?php

namespace App\Domain\Login\Entities;

class LoginDomainEntities
{

    #declare property for store data
    private int $id;
    private string $email;
    private string $token;

    #inject data: id,email & token(as session)
    public function __construct(int $id, string $email, string $token)
    {
        $this->id = $id;
        $this->email = $email;
        $this->token = $token;
    }

    #declare method for return data as type
    public function GetID(): ?int
    {
        return $this->id ?? null;
    }

    public function GetEmail(): ?string
    {
        return $this->email ?? null;
    }

    public function GetToken(): ?string
    {
        return $this->token ?? null;
    }
}
