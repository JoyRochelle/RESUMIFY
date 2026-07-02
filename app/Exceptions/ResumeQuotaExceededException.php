<?php

namespace App\Exceptions;

use RuntimeException;

class ResumeQuotaExceededException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Basic accounts can create 1 resume. Upgrade to Premium for unlimited resumes.');
    }
}
