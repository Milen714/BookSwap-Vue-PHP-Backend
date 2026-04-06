<?php

namespace App\Exceptions;

class ValidationException extends ApplicationException
{
    public function __construct(string $message = "Validation failed")
    {
        parent::__construct($message, 400);
    }
}
