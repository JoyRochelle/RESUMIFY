<?php

namespace App\Actions\Interviews;

class EndInterviewSessionResult
{
    private function __construct(public readonly string $status) {}

    public static function feedbackGenerated(): self
    {
        return new self('feedback_generated');
    }

    public static function feedbackUnavailable(): self
    {
        return new self('feedback_unavailable');
    }

    public static function feedbackFailed(): self
    {
        return new self('feedback_failed');
    }

    public function feedbackGeneratedSuccessfully(): bool
    {
        return $this->status === 'feedback_generated';
    }

    public function feedbackUnavailableDueToQuota(): bool
    {
        return $this->status === 'feedback_unavailable';
    }
}
