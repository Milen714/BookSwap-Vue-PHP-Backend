<?php

namespace App\Exceptions;

class RequiredFieldException extends ApplicationException
{
    public function __construct(string $message = "A required field is missing.")
    {
        parent::__construct($message, 400);
    }
}