<?php

namespace App\Exceptions;

class RequiredFieldException extends \Exception
{
    public function __construct(string $message = "A required field is missing.")
    {
        parent::__construct($message);
    }
}