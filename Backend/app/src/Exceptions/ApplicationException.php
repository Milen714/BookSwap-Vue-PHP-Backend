<?php

namespace App\Exceptions;

class ApplicationException extends \Exception
{
    public function __construct(string $message = "", int $code = 500, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getHttpStatusCode(): int
    {
        return $this->getCode() > 0 ? $this->getCode() : 500;
    }
}