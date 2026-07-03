<?php

namespace App\Exceptions;

use RuntimeException;

class InvalidAiProviderResponseException extends RuntimeException
{
    public function __construct(string $message = 'AI provider returned an invalid response.')
    {
        parent::__construct($message);
    }
}
