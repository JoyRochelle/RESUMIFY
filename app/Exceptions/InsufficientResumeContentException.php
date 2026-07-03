<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientResumeContentException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Your CV does not have enough content to tailor. Please fill in your resume sections with more details first (at least 200 characters).');
    }
}
