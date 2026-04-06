<?php

namespace App\Exceptions;

class ServiceException extends ApplicationException
{
    public function __construct(string $message = "Service operation failed", int $code = 500, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
