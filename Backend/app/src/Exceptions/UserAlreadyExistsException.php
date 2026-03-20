<?php

namespace App\Exceptions;

class UserAlreadyExistsException extends ApplicationException
{
    public function __construct(string $message = "User with this email already exists")
    {
        parent::__construct($message);
    }
}
