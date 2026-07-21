<?php

namespace App\Support\Concerns;

use App\Support\GeminiUsage;

/**
 * Accumulates Gemini token usage for the current logical operation on a
 * service. A public entry point resets the tally; each underlying HTTP
 * response records into it; the caller reads {@see lastUsage()} afterwards to
 * persist real token counts and cost to ai_usage_logs.
 *
 * Safe because PHP requests are synchronous: within a single request the calls
 * that share a service instance run sequentially, never interleaved.
 */
trait TracksGeminiUsage
{
    private ?GeminiUsage $lastUsage = null;

    /**
     * Token usage of the most recent operation. Never null — an operation that
     * made no (or only faked) Gemini calls reports zero usage.
     */
    public function lastUsage(): GeminiUsage
    {
        return $this->lastUsage ??= new GeminiUsage();
    }

    /**
     * Start a fresh tally for a new billable operation.
     */
    protected function resetUsage(): void
    {
        $this->lastUsage = new GeminiUsage();
    }

    /**
     * Fold one Gemini response's usage into the running tally.
     */
    protected function recordUsage(?array $responseJson): void
    {
        $this->lastUsage = $this->lastUsage()->plus(GeminiUsage::fromResponse($responseJson));
    }
}
