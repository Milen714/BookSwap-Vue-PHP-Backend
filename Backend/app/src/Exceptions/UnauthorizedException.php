<?php

namespace App\Exceptions;

class UnauthorizedException extends ApplicationException
{
    public function __construct(string $message = "Authentication required")
    {
        parent::__construct($message, 401);
    }
}
