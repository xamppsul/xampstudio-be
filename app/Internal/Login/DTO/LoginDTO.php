<?php

namespace App\Internal\Login\DTO;

class LoginDTO
{

    #declare property store email & password
    public string $email, $password;

    #receive data email & password
    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }
}
