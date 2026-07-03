<?php

namespace App\Actions\Ai;

use App\Exceptions\AiQuotaExceededException;
use App\Models\Cv;
use App\Models\User;
use App\Services\AiCreditService;
use App\Services\AiService;

class RefineResumeBulletAction
{
    public function __construct(
        private AiService $aiService,
        private AiCreditService $aiCreditService,
    ) {}

    public function execute(User $user, Cv $cv, string $text, ?string $jobContext): array
    {
        $reservation = $this->aiCreditService->reserve($user, 1, 'bullet_optimize');

        if ($reservation->isDenied()) {
            throw new AiQuotaExceededException(
                remainingCredits: $reservation->remainingCredits(),
                quotaLimit: $reservation->quotaLimit,
            );
        }

        try {
            $options = $this->aiService->refineBullet($text, $jobContext);
            $this->aiService->logUsage($user->id, 'bullet_optimize', $cv->id);

            return $options;
        } catch (\Exception $e) {
            $this->aiCreditService->refund($reservation);

            throw $e;
        }
    }
}
