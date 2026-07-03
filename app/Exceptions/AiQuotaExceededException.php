<?php

namespace App\Exceptions;

use RuntimeException;

class AiQuotaExceededException extends RuntimeException
{
    public function __construct(
        public readonly int $remainingCredits,
        public readonly int $quotaLimit,
    ) {
        parent::__construct('You have used all your AI credits. Upgrade to Premium for 50 credits/month.');
    }
}
