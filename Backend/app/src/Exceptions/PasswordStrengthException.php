<?php

namespace App\Exceptions;

class PasswordStrengthException extends \Exception
{
    public function __construct(string $message = "Password does not meet the required strength criteria.")
    {
        parent::__construct($message);
    }
}