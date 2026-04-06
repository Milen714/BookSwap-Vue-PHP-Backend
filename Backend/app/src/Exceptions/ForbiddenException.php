<?php

namespace App\Exceptions;

class ForbiddenException extends ApplicationException
{
    public function __construct(string $message = "You are not allowed to perform this action")
    {
        parent::__construct($message, 403);
    }
}
