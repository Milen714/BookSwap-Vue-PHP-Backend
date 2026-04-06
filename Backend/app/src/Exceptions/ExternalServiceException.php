<?php

namespace App\Exceptions;

class ExternalServiceException extends ApplicationException
{
    public function __construct(string $message = "External service request failed", ?\Throwable $previous = null)
    {
        parent::__construct($message, 502, $previous);
    }
}
