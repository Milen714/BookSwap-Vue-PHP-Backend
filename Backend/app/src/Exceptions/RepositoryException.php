<?php

namespace App\Exceptions;

class RepositoryException extends ApplicationException
{
    public function __construct(string $message = "Database operation failed", ?\Throwable $previous = null)
    {
        parent::__construct($message, 500, $previous);
    }
}
